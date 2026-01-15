<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use OpenAI\Client;
use OpenAI\Factory;

class EduService
{
    private static function getClient(): Client
    {
        return (new Factory())
            ->withApiKey(config('openai.api_key'))
            ->withHttpClient(new \GuzzleHttp\Client([
                'verify' => config('openai.verify', false),
                'timeout' => config('openai.request_timeout', 30),
            ]))
            ->make();
    }

    public static function generateEduResponse(int $grade, string $subject, string $topic): array
    {
        $prompt = "You are a master teacher for the Nepal NEB curriculum.

Create a comprehensive, full-length lesson for Grade {$grade} {$subject} on the topic: \"{$topic}\".
The lesson must be detailed enough to cover a 45-minute class.

Return ONLY valid JSON in the following structure:

{
  \"concept\": \"string\",
  \"objectives\": [\"string\"],
  \"explanation\": \"string\",
  \"nepalContext\": \"string\",
  \"keyPoints\": [\"string\"],
  \"analogy\": \"string\",
  \"questions\": [
    {
      \"question\": \"string\",
      \"options\": [\"string\", \"string\", \"string\", \"string\"],
      \"correct_answer\": \"string\"
    }
  ],
  \"followUpSuggestions\": [\"string\"]
}

Rules:
- Exactly 5 questions
- Options must be meaningful (not Option A/B)
- correct_answer MUST match one of the options
- No markdown
- No explanation outside JSON";

        try {
            $client = self::getClient();
            $response = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);

            $content = $response->choices[0]->message->content;
            $result = json_decode($content, true);

            if (
                $result &&
                isset($result['concept'], $result['questions']) &&
                is_array($result['questions'])
            ) {
                return [
                    'title' => $result['concept'],
                    'description' => "Comprehensive lesson on {$topic} for Grade {$grade} {$subject}",
                    'objectives' => $result['objectives'] ?? [],
                    'content' => $result['explanation'] ?? '',
                    'nepal_context' => $result['nepalContext'] ?? '',
                    'key_points' => $result['keyPoints'] ?? [],
                    'analogy' => $result['analogy'] ?? '',
                    'questions' => array_values(array_filter(
                        $result['questions'],
                        fn($q) =>
                        isset($q['question'], $q['options'], $q['correct_answer']) &&
                            is_array($q['options']) &&
                            count($q['options']) === 4
                    )),
                    'follow_up_suggestions' => $result['followUpSuggestions'] ?? []
                ];
            }
        } catch (\Exception $e) {
            Log::error('OpenAI API error in generateEduResponse: ' . $e->getMessage());
        }

        // Fallback to syllabus content if API fails
        return self::getFallbackLesson($grade, $subject, $topic);
    }

    public static function eduFollowUp(string $context, string $question): string
    {
        $prompt = "Based on this lesson context: \"{$context}\", answer this student follow-up question: \"{$question}\". Keep the explanation simple, encouraging, and accurate to the NEB syllabus. Provide a helpful and educational response.";

        try {
            $client = self::getClient();
            $response = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            return $response->choices[0]->message->content ?: "I'm sorry, I couldn't process that question. Could you try rephrasing?";
        } catch (\Exception $e) {
            Log::error('OpenAI API error in eduFollowUp: ' . $e->getMessage());
            return "I'm sorry, I couldn't process that question right now. Please try again later.";
        }
    }

    private static function getFallbackLesson(int $grade, string $subject, string $topic): array
    {
        return [
            'title' => ucfirst($topic),
            'description' => "Learn about {$topic} in {$subject} for Grade {$grade}",
            'objectives' => [
                "Understand the basic concepts of {$topic}",
                "Learn key principles and applications",
                "Apply knowledge to solve problems"
            ],
            'content' => "<h3>{$topic}</h3><p>This topic covers important concepts in {$subject} for Grade {$grade} students. The content is designed to help you understand the fundamental principles and their applications.</p><p>Key learning points include:</p><ul><li>Understanding basic concepts</li><li>Learning important definitions</li><li>Exploring real-world applications</li><li>Practicing problem-solving skills</li></ul>",
            'nepal_context' => "This topic relates to Nepal through our geography, culture, and daily life. Nepal's diverse landscape and rich cultural heritage provide excellent examples for understanding {$topic}.",
            'key_points' => ["Study regularly", "Practice problems", "Ask questions when needed"],
            'analogy' => "Think of {$topic} like the diverse landscapes of Nepal - from the high Himalayas to the fertile Terai plains, each part has its own unique characteristics and importance.",
            'questions' => [
                ['question' => "What is the main concept of {$topic}?", 'options' => ['Basic idea', 'Advanced concept', 'Simple definition', 'Complex theory'], 'correct_answer' => 'Basic idea'],
                ['question' => "Why is {$topic} important to study?", 'options' => ['For exams', 'For understanding', 'For knowledge', 'All of these'], 'correct_answer' => 'All of these'],
                ['question' => "How can you apply {$topic} in daily life?", 'options' => ['In school', 'At home', 'Everywhere', 'Nowhere'], 'correct_answer' => 'Everywhere']
            ],
            'follow_up_suggestions' => ["Practice more examples", "Discuss with classmates", "Ask teacher for clarification"]
        ];
    }
    /* ===========================
       AI QUIZ (25 QUESTIONS)
    ============================ */
    public static function generateQuiz(int $grade, string $topic): array
    {
        $prompt = <<<PROMPT
Generate 25 multiple-choice quiz questions.

Grade: {$grade}
Topic: {$topic}

Rules:
- Medium difficulty
- NEB aligned
- Each question has 4 options
- Clearly indicate correct answer

Return STRICT JSON:

[
 {
  "question": "string",
  "options": ["A","B","C","D"],
  "answer": "A"  // or the exact option text
 }
]
PROMPT;

        try {
            $attempts = 3;
            $lastContent = null;

            for ($attempt = 1; $attempt <= $attempts; $attempt++) {
                // Ask AI for strict JSON output
                $content = OpenAIService::simpleChat([
                    ['role' => 'user', 'content' => $prompt]
                ], 'gpt-3.5-turbo', 0.2, 2500);

                $lastContent = $content;

                if (! $content) {
                    \Log::warning('Empty response from OpenAI when generating quiz', ['attempt' => $attempt]);
                    sleep((int) pow(2, $attempt));
                    continue;
                }

                \Log::debug('Raw quiz response from OpenAI', ['attempt' => $attempt, 'snippet' => substr($content, 0, 2000)]);

                // Try parsing as JSON
                $raw = json_decode($content, true);

                // If decoding failed, try to extract a JSON array from the response body
                if (!is_array($raw) && preg_match('/(\[\s*\{.*\}\s*\])/s', $content, $m)) {
                    $raw = json_decode($m[1], true);
                }

                // If still not parsed, ask the model to reformat only the JSON
                if (!is_array($raw)) {
                    \Log::warning('Could not parse quiz JSON from OpenAI, requesting reformat', ['attempt' => $attempt]);

                    $reformatPrompt = "Please ONLY return a strict JSON array of multiple-choice questions (no explanation).\n\nOriginal response:\n" . $content . "\n\nEnsure each item has: question (string), options (array of 4 strings), answer (exact option text).";

                    $re = OpenAIService::simpleChat([
                        ['role' => 'system', 'content' => 'You are a JSON formatter. Return only valid JSON.'],
                        ['role' => 'user', 'content' => $reformatPrompt]
                    ], 'gpt-3.5-turbo', 0.0, 1200);

                    if ($re) {
                        $raw = json_decode($re, true);
                        if (!is_array($raw) && preg_match('/(\[\s*\{.*\}\s*\])/s', $re, $mm)) {
                            $raw = json_decode($mm[1], true);
                        }
                        $lastContent = $re;
                    }
                }

                if (!is_array($raw)) {
                    \Log::warning('Could not parse quiz JSON after reformat attempt', ['attempt' => $attempt, 'snippet' => substr($lastContent ?? '', 0, 500)]);
                    sleep((int) pow(2, $attempt));
                    continue;
                }

                // Normalize and validate
                $normalized = [];
                foreach ($raw as $item) {
                    if (!isset($item['question'], $item['options'])) continue;
                    $options = $item['options'];
                    if (!is_array($options) || count($options) < 2) continue;

                    // ensure options are plain strings
                    $options = array_values(array_map(fn($o) => is_scalar($o) ? (string)$o : json_encode($o), $options));

                    // Determine the correct answer text
                    $answer = $item['answer'] ?? ($item['correct_answer'] ?? null);
                    $answerText = null;

                    if ($answer !== null) {
                        $trim = trim((string)$answer);
                        // If answer is a single letter (A-D), convert to option text
                        if (preg_match('/^[A-D]$/i', $trim)) {
                            $idx = ord(strtoupper($trim)) - ord('A');
                            $answerText = $options[$idx] ?? null;
                        } elseif (preg_match('/^[1-4]$/', $trim)) {
                            $idx = (int)$trim - 1;
                            $answerText = $options[$idx] ?? null;
                        } else {
                            // If the provided answer matches one of the options (case-insensitive), use canonical option text
                            foreach ($options as $opt) {
                                if (strcasecmp(trim($opt), $trim) === 0) {
                                    $answerText = $opt;
                                    break;
                                }
                            }
                            // As last resort, if answer is small (like 'Option A'), try extracting letter
                            if (!$answerText && preg_match('/([A-D])/i', $trim, $m)) {
                                $idx = ord(strtoupper($m[1])) - ord('A');
                                $answerText = $options[$idx] ?? null;
                            }
                        }
                    }

                    // If we still don't have an answer text, default to first option
                    if (!$answerText) {
                        $answerText = $options[0];
                    }

                    $normalized[] = [
                        'question' => trim((string)$item['question']),
                        'options' => $options,
                        'answer' => $answerText
                    ];
                }

                if (empty($normalized)) {
                    \Log::warning('Normalized quiz empty after parsing', ['attempt' => $attempt]);
                    sleep((int) pow(2, $attempt));
                    continue;
                }

                // Return full normalized quiz
                return $normalized;
            }

            // If we exhausted attempts, throw so controller returns 500 (no fallbacks per request)
            \Log::error('Quiz generation failed after multiple attempts', ['last_snippet' => substr($lastContent ?? '', 0, 500)]);
            throw new \RuntimeException('Failed to generate quiz from AI');
        } catch (\Throwable $e) {
            \Log::error('OpenAI error in generateQuiz: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    /* ===========================
       AI FLASH CARDS (25)
    ============================ */
    public static function generateFlashCards(int $grade, string $topic): array
    {
        $prompt = <<<PROMPT
Generate 25 educational flash cards.

Grade: {$grade}
Topic: {$topic}

Rules:
- Simple front, clear back
- Exam focused
- Concept clarity

Return STRICT JSON:

[
 {
  "front": "question / term",
  "back": "clear explanation"
 }
]
PROMPT;

        try {
            // Try a few times (exponential backoff) in case of transient OpenAI errors
            $content = null;
            $attempts = 0;
            while ($attempts < 3 && !$content) {
                $content = OpenAIService::simpleChat([
                    ['role' => 'user', 'content' => $prompt]
                ], 'gpt-3.5-turbo', 0.7, 2500);

                if ($content) break;
                $wait = (int) pow(2, $attempts);
                \Log::warning('Empty response from OpenAI when generating flashcards; retrying', ['attempt' => $attempts, 'wait' => $wait]);
                sleep(max(1, $wait));
                $attempts++;
            }

            if (! $content) {
                \Log::error('Failed to generate flashcards after retries');
                return [];
            }

            // Try to parse JSON
            $raw = json_decode($content, true);
            if (!is_array($raw) && preg_match('/(\[\s*\{.*\}\s*\])/s', $content, $m)) {
                $raw = json_decode($m[1], true);
            }

            return is_array($raw) ? $raw : [];
        } catch (\Throwable $e) {
            \Log::error('Flash card generation error ' . $e->getMessage());
            return [];
        }
    }
}
