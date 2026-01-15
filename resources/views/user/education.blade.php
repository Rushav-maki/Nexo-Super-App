@extends('layouts.app')

@section('title', 'EduSync - Academic Intelligence System')

@section('content')

    <div x-data="eduSyncApp()" class="max-w-7xl mx-auto space-y-16 pb-40 text-stone-900 w-full animate-reveal">

        <!-- HEADER -->
        <section class="flex justify-between items-end border-b pb-10">
            <h2 class="text-7xl font-serif italic font-bold">
                Edu<span class="text-orange-600">.Sync</span>
                <span class="text-orange-600">by Nexo</span>
            </h2>

            <nav
                class="flex bg-stone-100 p-1.5 rounded-[1.5rem] border border-black/5 w-fit shadow-inner overflow-x-auto no-scrollbar">
                <template x-for="m in ['Hub','Library','AI Lab']" :key="m">
                    <button @click="setMode(m)" :class="mode === m ? 'bg-black text-white' : 'bg-gray-100'"
                        class="px-6 py-3 rounded-full text-xs font-bold uppercase" x-text="m"></button>
                </template>
            </nav>
        </section>

        <!-- HUB -->
        <div x-show="mode === 'Hub'" x-transition.opacity.duration.300ms>
            <div x-data="hubAI()" class="max-w-7xl mx-auto space-y-16">

                <!-- Hero Header -->
                <div
                    class="relative overflow-hidden bg-gradient-to-br from-stone-900 via-stone-800 to-black text-white p-16 rounded-[3rem] shadow-2xl">
                    <div
                        class="absolute top-0 right-0 w-96 h-96 bg-orange-500/20 rounded-full blur-3xl animate-[pulse_8s_ease-in-out_infinite]">
                    </div>
                    <div class="absolute bottom-0 left-0 w-96 h-96 bg-orange-600/10 rounded-full blur-3xl animate-[pulse_10s_ease-in-out_infinite]"
                        style="animation-delay: 2s;"></div>

                    <div class="relative z-10 text-center space-y-6">
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500/20 rounded-full border border-orange-500/30 backdrop-blur-sm mb-4">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                            </span>
                            <span class="text-xs font-bold text-orange-300 uppercase tracking-wider">AI-Powered
                                Learning</span>
                        </div>

                        <h2
                            class="text-7xl font-serif italic font-black bg-gradient-to-r from-white via-orange-200 to-white bg-clip-text text-transparent">
                            Edu<span class="text-orange-500">.AI</span> Hub
                        </h2>

                        <p class="text-xl text-white/70 max-w-2xl mx-auto font-medium">
                            Generate intelligent quizzes and flashcards powered by advanced AI technology
                        </p>
                    </div>
                </div>

                <!-- Input Section -->
                <div class="relative">
                    <div
                        class="absolute -inset-1 bg-gradient-to-r from-orange-600 to-orange-400 rounded-3xl blur opacity-20">
                    </div>
                    <div class="relative bg-white rounded-3xl shadow-xl p-10 space-y-8">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center">
                                <i class="fa-solid fa-sliders text-white"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-stone-900">Configure Your Learning</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label
                                    class="text-xs font-black text-stone-500 uppercase tracking-wider flex items-center gap-2">
                                    <i class="fa-solid fa-graduation-cap text-orange-600"></i>
                                    Grade Level
                                </label>
                                <input x-model="grade" type="number" min="3" max="10"
                                    placeholder="Enter grade (3-10)"
                                    class="w-full px-6 py-4 rounded-2xl border-2 border-stone-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none font-semibold text-lg">
                            </div>

                            <div class="space-y-3">
                                <label
                                    class="text-xs font-black text-stone-500 uppercase tracking-wider flex items-center gap-2">
                                    <i class="fa-solid fa-book-open text-orange-600"></i>
                                    Topic
                                </label>
                                <input x-model="topic" placeholder="e.g. Photosynthesis, Fractions..."
                                    class="w-full px-6 py-4 rounded-2xl border-2 border-stone-200 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all outline-none font-semibold text-lg">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                            <button @click="loadQuiz()" :disabled="loadingQuiz || !grade || !topic"
                                class="group relative px-8 py-5 bg-gradient-to-r from-stone-900 to-stone-800 text-white rounded-2xl font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 overflow-hidden">
                                <span class="relative flex items-center justify-center gap-3">
                                    <i class="fa-solid fa-clipboard-question"></i>
                                    <span x-show="!loadingQuiz">Generate Quiz</span>
                                    <span x-show="loadingQuiz">Generating...</span>
                                </span>
                            </button>

                            <button @click="loadFlash()" :disabled="loadingFlash || !grade || !topic"
                                class="group relative px-8 py-5 bg-gradient-to-r from-orange-600 to-orange-500 text-white rounded-2xl font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                                <span class="relative flex items-center justify-center gap-3">
                                    <i class="fa-solid fa-layer-group"></i>
                                    <span x-show="!loadingFlash">Generate Flashcards</span>
                                    <span x-show="loadingFlash">Generating...</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- INTERACTIVE QUIZ -->
                <template x-if="quiz.length">
                    <section class="space-y-8 opacity-0 animate-[fadeIn_0.6s_ease-out_forwards]">
                        <div
                            class="flex items-center justify-between p-8 bg-gradient-to-r from-stone-900 to-stone-800 text-white rounded-3xl">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center">
                                    <i class="fa-solid fa-brain text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-bold font-serif italic">Interactive Quiz</h3>
                                    <p class="text-white/70">Grade <span x-text="grade"></span> - <span
                                            x-text="topic"></span></p>
                                </div>
                            </div>
                            <div x-show="!quizCompleted" class="text-right">
                                <p class="text-sm text-white/60 font-bold uppercase tracking-wider">Question</p>
                                <p class="text-3xl font-black font-serif"><span x-text="currentQuestion + 1"></span>/<span
                                        x-text="quiz.length"></span></p>
                            </div>
                        </div>

                        <template x-if="!quizCompleted">
                            <div class="relative">
                                <div
                                    class="absolute -inset-1 bg-gradient-to-r from-orange-600 to-orange-400 rounded-3xl blur opacity-20">
                                </div>
                                <div class="relative bg-white rounded-3xl shadow-2xl p-10 space-y-8">
                                    <template x-for="(q, i) in quiz" :key="i">
                                        <div x-show="i === currentQuestion" x-transition class="space-y-8">
                                            <div class="flex items-start gap-4">
                                                <div
                                                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                                    <span x-text="i + 1"></span>
                                                </div>
                                                <p class="text-2xl font-bold text-stone-900 leading-relaxed"
                                                    x-text="q.question"></p>
                                            </div>

                                            <div class="grid gap-4">
                                                <template x-for="(option, idx) in q.options" :key="idx">
                                                    <button @click="selectAnswer(i, idx, option)"
                                                        :disabled="userAnswers[i] !== undefined"
                                                        :class="{
                                                            'border-green-500 bg-green-50': userAnswers[i] !==
                                                                undefined && option === q.answer,
                                                            'border-red-500 bg-red-50': userAnswers[i] !== undefined &&
                                                                userAnswers[i].selectedIndex === idx && option !== q
                                                                .answer,
                                                            'border-stone-200 hover:border-orange-300 hover:bg-orange-50/50': userAnswers[
                                                                i] === undefined
                                                        }"
                                                        class="group text-left px-6 py-5 rounded-2xl border-2 transition-all duration-300 disabled:cursor-not-allowed">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center gap-4">
                                                                <div class="w-10 h-10 rounded-xl border-2 flex items-center justify-center font-bold text-sm transition-all"
                                                                    :class="{
                                                                        'border-green-500 bg-green-500 text-white': userAnswers[
                                                                            i] !== undefined && option === q.answer,
                                                                        'border-red-500 bg-red-500 text-white': userAnswers[
                                                                                i] !== undefined && userAnswers[i]
                                                                            .selectedIndex === idx && option !== q
                                                                            .answer,
                                                                        'border-stone-300 text-stone-600 group-hover:border-orange-500': userAnswers[
                                                                            i] === undefined
                                                                    }">
                                                                    <span x-text="String.fromCharCode(65 + idx)"></span>
                                                                </div>
                                                                <span class="text-lg font-semibold text-stone-700"
                                                                    x-text="option"></span>
                                                            </div>
                                                            <div x-show="userAnswers[i] !== undefined">
                                                                <i x-show="option === q.answer"
                                                                    class="fa-solid fa-circle-check text-2xl text-green-500"></i>
                                                                <i x-show="userAnswers[i].selectedIndex === idx && option !== q.answer"
                                                                    class="fa-solid fa-circle-xmark text-2xl text-red-500"></i>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </template>
                                            </div>

                                            <div class="flex items-center justify-between pt-6 border-t border-stone-100">
                                                <button @click="previousQuestion()" x-show="currentQuestion > 0"
                                                    class="px-6 py-3 rounded-xl border-2 border-stone-200 text-stone-700 font-bold hover:border-stone-300 hover:bg-stone-50 transition-all">
                                                    <i class="fa-solid fa-arrow-left mr-2"></i> Previous
                                                </button>
                                                <div></div>
                                                <button @click="nextQuestion()" x-show="currentQuestion < quiz.length - 1"
                                                    :disabled="userAnswers[currentQuestion] === undefined"
                                                    class="px-6 py-3 rounded-xl bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                                    Next <i class="fa-solid fa-arrow-right ml-2"></i>
                                                </button>
                                                <button @click="completeQuiz()"
                                                    x-show="currentQuestion === quiz.length - 1"
                                                    :disabled="userAnswers[currentQuestion] === undefined"
                                                    class="px-8 py-3 rounded-xl bg-gradient-to-r from-green-600 to-green-500 text-white font-bold hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                                    Complete Quiz <i class="fa-solid fa-check ml-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Quiz Results -->
                        <template x-if="quizCompleted">
                            <div class="relative">
                                <div
                                    class="absolute -inset-1 bg-gradient-to-r from-green-600 to-green-400 rounded-3xl blur opacity-30">
                                </div>
                                <div class="relative bg-white rounded-3xl shadow-2xl p-12 text-center space-y-8">
                                    <div
                                        class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-gradient-to-br from-green-500 to-green-600 text-white shadow-2xl">
                                        <i class="fa-solid fa-trophy text-6xl"></i>
                                    </div>

                                    <div class="space-y-4">
                                        <h3 class="text-6xl font-serif italic font-black text-stone-900">Quiz Complete!
                                        </h3>
                                        <p class="text-2xl text-stone-600 font-medium">Here's how you performed</p>
                                    </div>

                                    <div
                                        class="inline-flex items-center gap-8 px-12 py-8 bg-gradient-to-br from-stone-50 to-white rounded-3xl border-2 border-stone-100 shadow-lg">
                                        <div class="text-center">
                                            <p class="text-sm text-stone-500 font-black uppercase tracking-wider mb-2">Your
                                                Score</p>
                                            <p
                                                class="text-7xl font-black font-serif bg-gradient-to-br from-green-600 to-green-500 bg-clip-text text-transparent">
                                                <span x-text="calculateScore()"></span>/<span x-text="quiz.length"></span>
                                            </p>
                                        </div>
                                        <div class="h-20 w-px bg-stone-200"></div>
                                        <div class="text-center">
                                            <p class="text-sm text-stone-500 font-black uppercase tracking-wider mb-2">
                                                Percentage</p>
                                            <p
                                                class="text-7xl font-black font-serif bg-gradient-to-br from-orange-600 to-orange-500 bg-clip-text text-transparent">
                                                <span x-text="Math.round((calculateScore() / quiz.length) * 100)"></span>%
                                            </p>
                                        </div>
                                    </div>

                                    <div class="p-6 rounded-2xl"
                                        :class="calculateScore() / quiz.length >= 0.8 ? 'bg-green-50 border border-green-200' :
                                            'bg-orange-50 border border-orange-200'">
                                        <p class="text-xl font-bold"
                                            :class="calculateScore() / quiz.length >= 0.8 ? 'text-green-700' : 'text-orange-700'">
                                            <span x-show="calculateScore() / quiz.length >= 0.8">🎉 Excellent work! You've
                                                mastered this topic!</span>
                                            <span x-show="calculateScore() / quiz.length < 0.8">📚 Good effort! Review the
                                                lesson to improve your understanding.</span>
                                        </p>
                                    </div>

                                    <div class="flex gap-4 justify-center pt-4">
                                        <button @click="resetQuiz()"
                                            class="px-8 py-4 rounded-2xl bg-gradient-to-r from-orange-600 to-orange-500 text-white font-bold text-lg hover:shadow-lg transition-all hover:scale-105">
                                            <i class="fa-solid fa-rotate-right mr-2"></i> Retake Quiz
                                        </button>
                                        <button @click="quiz = []; quizCompleted = false;"
                                            class="px-8 py-4 rounded-2xl border-2 border-stone-200 text-stone-700 font-bold text-lg hover:border-stone-300 hover:bg-stone-50 transition-all">
                                            <i class="fa-solid fa-arrow-left mr-2"></i> New Quiz
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </section>
                </template>

                <!-- FLASHCARDS -->
                <template x-if="cards.length">
                    <section class="space-y-8 opacity-0 animate-[fadeIn_0.6s_ease-out_0.3s_forwards]">
                        <div
                            class="flex items-center justify-between p-8 bg-gradient-to-r from-orange-600 to-orange-500 text-white rounded-3xl">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center">
                                    <i class="fa-solid fa-layer-group text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-bold font-serif italic">Flashcards</h3>
                                    <p class="text-white/80"><span x-text="cards.length"></span> cards generated</p>
                                </div>
                            </div>
                            <button @click="shuffleCards()"
                                class="px-6 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl font-bold transition-all">
                                <i class="fa-solid fa-shuffle mr-2"></i> Shuffle
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <template x-for="(card, index) in cards" :key="index">
                                <div @click="flipCard(index)" class="group cursor-pointer"
                                    style="perspective: 1000px; min-height: 280px;">
                                    <div class="relative w-full h-full transition-transform duration-700"
                                        :class="flippedCards[index] ? 'rotate-y-180' : ''"
                                        style="transform-style: preserve-3d;">

                                        <!-- Front -->
                                        <div class="absolute inset-0" style="backface-visibility: hidden;">
                                            <div class="relative h-full">
                                                <div
                                                    class="absolute -inset-0.5 bg-gradient-to-r from-orange-600 to-orange-400 rounded-3xl blur opacity-30 group-hover:opacity-50 transition-opacity">
                                                </div>
                                                <div
                                                    class="relative h-full bg-white rounded-3xl shadow-xl p-8 flex flex-col justify-between hover:shadow-2xl transition-all">
                                                    <div class="space-y-4">
                                                        <div class="flex items-center justify-between">
                                                            <div
                                                                class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">
                                                                Question</div>
                                                            <span class="text-sm font-bold text-stone-400">#<span
                                                                    x-text="index + 1"></span></span>
                                                        </div>
                                                        <p class="text-xl font-bold text-stone-900 leading-relaxed"
                                                            x-text="card.front"></p>
                                                    </div>
                                                    <div
                                                        class="flex items-center justify-center gap-2 text-orange-600 text-sm font-bold">
                                                        <i class="fa-solid fa-hand-pointer animate-pulse"></i>
                                                        Click to reveal
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Back -->
                                        <div class="absolute inset-0"
                                            style="backface-visibility: hidden; transform: rotateY(180deg);">
                                            <div class="relative h-full">
                                                <div
                                                    class="absolute -inset-0.5 bg-gradient-to-r from-green-600 to-green-400 rounded-3xl blur opacity-30">
                                                </div>
                                                <div
                                                    class="relative h-full bg-gradient-to-br from-green-600 to-green-500 rounded-3xl shadow-xl p-8 flex flex-col justify-between text-white">
                                                    <div class="space-y-4">
                                                        <div class="flex items-center justify-between">
                                                            <div
                                                                class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold">
                                                                Answer</div>
                                                            <i class="fa-solid fa-check-circle text-2xl"></i>
                                                        </div>
                                                        <p class="text-lg font-semibold leading-relaxed"
                                                            x-text="card.back"></p>
                                                    </div>
                                                    <div
                                                        class="flex items-center justify-center gap-2 text-white/80 text-sm font-bold">
                                                        <i class="fa-solid fa-rotate-left"></i>
                                                        Click to flip
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="flex items-center justify-center gap-4">
                            <button @click="cards = []; flippedCards = {}"
                                class="px-6 py-3 rounded-xl border-2 border-stone-200 text-stone-700 font-bold hover:border-stone-300 hover:bg-stone-50 transition-all">
                                <i class="fa-solid fa-xmark mr-2"></i> Clear Cards
                            </button>
                        </div>
                    </section>
                </template>

            </div>
        </div>

        <!-- LIBRARY -->
        <div x-show="mode === 'Library'" class="space-y-14">

            <template x-for="(files, grade) in libraryResources" :key="grade">
                <section class="space-y-6">

                    <!-- Grade Header -->
                    <h3 class="text-3xl font-serif italic">
                        Grade <span x-text="grade"></span>
                    </h3>

                    <!-- Files Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <template x-for="(file, i) in files" :key="i">
                            <div
                                class="group relative bg-gray-50 rounded-3xl p-6 shadow
                               flex flex-col justify-between
                               transition-all duration-300 ease-out
                               hover:-translate-y-2 hover:scale-[1.02] hover:shadow-2xl hover:bg-white">

                                <!-- File Name -->
                                <h5 class="italic font-bold mb-6 text-sm text-stone-800
                                   transition-colors duration-300
                                   group-hover:text-orange-600"
                                    x-text="file">
                                </h5>

                                <!-- View PDF Button -->
                                <a :href="`/content/${encodeURIComponent(file)}`" target="_blank"
                                    rel="noopener noreferrer"
                                    class="relative text-xs font-bold uppercase text-orange-600
                                   after:absolute after:left-0 after:-bottom-1
                                   after:w-0 after:h-[2px] after:bg-orange-600
                                   after:transition-all after:duration-300
                                   group-hover:after:w-full">
                                    View PDF →
                                </a>
                            </div>
                        </template>
                    </div>

                </section>
            </template>

        </div>


        <!-- AI LAB -->
        <div x-show="mode === 'AI Lab'" x-transition.opacity.duration.500ms class="space-y-14">

            <!-- FORM -->
            <div x-show="!lesson" x-transition
                class="relative overflow-hidden bg-gradient-to-br from-black via-stone-900 to-black
               text-white p-14 rounded-[4rem] space-y-10 shadow-2xl">

                <!-- Glow -->
                <div class="absolute -top-32 -right-32 w-96 h-96 bg-orange-600/20 rounded-full blur-3xl"></div>

                <div class="relative space-y-10">

                    <!-- Title -->
                    <div class="space-y-2">
                        <h3 class="text-5xl font-serif italic">AI Lesson Lab</h3>
                        <p class="text-white/60 text-sm">
                            Personalized learning crafted instantly for your academic needs.
                        </p>
                    </div>

                    <!-- Grade Selector -->
                    <div class="space-y-3">
                        <p class="uppercase text-xs tracking-widest text-white/50">Select Grade</p>

                        <div class="grid grid-cols-5 gap-4">
                            <template x-for="g in [3,4,5,6,7,8,9,10]" :key="g">
                                <button @click="setGrade(g)"
                                    :class="selectedGrade === g ?
                                        'bg-orange-600 scale-105' :
                                        'bg-white/10 hover:bg-white/20'"
                                    class="rounded-2xl py-4 font-black transition-all duration-300">
                                    G-<span x-text="g"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Subject -->
                    <div class="space-y-3">
                        <p class="uppercase text-xs tracking-widest text-white/50">Subject</p>

                        <select x-model="selectedSubject"
                            class="w-full p-5 rounded-2xl
                            bg-white/10 text-white
                            focus:outline-none focus:ring-2 focus:ring-orange-600">

                            <option value="" disabled selected hidden>Select any subject</option>

                            <template x-for="s in subjects" :key="s.name">
                                <option x-text="s.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Topic -->
                    <div class="space-y-3">
                        <p class="uppercase text-xs tracking-widest text-white/50">Topic</p>

                        <input x-model="selectedTopic"
                            class="w-full p-5 rounded-2xl bg-white/10 focus:outline-none focus:ring-2 focus:ring-orange-600"
                            placeholder="e.g. Photosynthesis, Algebraic Expressions..." />
                    </div>

                    <!-- Generate Button -->
                    <button @click="generateLesson" :disabled="loading"
                        class="w-full bg-white text-black py-6 rounded-2xl font-black uppercase
                       hover:scale-[1.02] transition-all duration-300
                       disabled:opacity-60 disabled:cursor-not-allowed">
                        <span x-show="!loading">Generate Lesson</span>
                        <span x-show="loading" class="animate-pulse">Generating AI Content…</span>
                    </button>
                </div>
            </div>

            <!-- LESSON OUTPUT -->
            <template x-if="lesson">
                <div x-transition x-cloak
                    class="bg-gradient-to-br from-black via-stone-900 to-black
               text-white p-20 rounded-[4rem] space-y-14 shadow-2xl">

                    <!-- Back -->
                    <button @click="resetLesson" class="text-orange-600 text-xs uppercase hover:underline">
                        ← Create another lesson
                    </button>

                    <!-- Lesson Title -->
                    <h2 class="text-6xl font-serif italic leading-tight" x-text="lesson.title"></h2>
                    <p class="mt-3 text-xl text-white/80 font-semibold" x-text="lesson.description"></p>

                    <!-- Learning Objectives -->
                    <div class="mt-10">
                        <h3 class="text-3xl font-serif italic mb-4">Learning Objectives</h3>

                        <ul class="space-y-3">
                            <template x-for="(obj, index) in lesson.objectives" :key="'obj-' + index">
                                <li class="flex gap-3 text-xl text-white/90">
                                    <span class="text-purple-400 font-black">•</span>
                                    <span x-text="obj"></span>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- Lesson Content -->
                    <div class="mt-12 space-y-8">
                        <p class="text-xl leading-relaxed whitespace-pre-wrap text-white/90" x-text="lesson.content"></p>

                        <p class="text-xl leading-relaxed whitespace-pre-wrap text-white/90"
                            x-text="lesson.nepal_context"></p>
                    </div>

                    <!-- Key Points -->
                    <div class="mt-12">
                        <h3 class="text-3xl font-serif italic mb-4">Key Points</h3>

                        <ul class="grid md:grid-cols-2 gap-4">
                            <template x-for="(point, index) in lesson.key_points" :key="'kp-' + index">
                                <li class="bg-white/5 border border-white/10 rounded-xl p-4 text-lg text-white/90">
                                    <span x-text="point"></span>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- ANALOGY -->
                    <div class="mt-12 bg-white/5 border border-white/10 rounded-2xl p-6">
                        <h3 class="text-2xl font-serif italic mb-2">Analogy</h3>
                        <p class="text-xl text-white/90 leading-relaxed" x-text="lesson.analogy"></p>
                    </div>

                    <!-- PRACTICE QUESTIONS -->
                    <div class="mt-14">
                        <h3 class="text-3xl font-serif italic mb-6">Practice Questions</h3>

                        <template x-for="(q, qIndex) in lesson.questions" :key="'q-' + qIndex">
                            <div class="mb-8 bg-white/5 border border-white/10 rounded-2xl p-6 space-y-5">

                                <!-- Question -->
                                <p class="text-xl font-semibold" x-text="q.question"></p>

                                <!-- Options -->
                                <ul class="space-y-2 pl-4">
                                    <template x-for="(opt, oIndex) in q.options" :key="'opt-' + qIndex + '-' + oIndex">
                                        <li class="text-white/80 flex gap-2">
                                            <span>•</span>
                                            <span x-text="opt"></span>
                                        </li>
                                    </template>
                                </ul>

                                <!-- Correct Answer -->
                                <div class="mt-4 pt-4 border-t border-white/10">
                                    <p class="text-sm uppercase tracking-widest text-emerald-400 font-bold">
                                        Correct Answer
                                    </p>
                                    <p class="text-lg text-white/90 font-semibold" x-text="q.correct_answer"></p>
                                </div>

                            </div>
                        </template>
                    </div>

                    <!-- FOLLOW-UP ACTIVITIES -->
                    <div class="mt-14">
                        <h3 class="text-3xl font-serif italic mb-4">Follow-Up Activities</h3>

                        <ul class="space-y-3">
                            <template x-for="(item, index) in lesson.follow_up_suggestions" :key="'fu-' + index">
                                <li class="text-xl text-white/90 flex gap-3">
                                    <span class="text-emerald-400">→</span>
                                    <span x-text="item"></span>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- FOLLOW UPS -->
                    <div class="space-y-8 border-t border-white/10 pt-10">

                        <h4 class="uppercase text-xs tracking-widest text-white/50">
                            Ask Follow-Up Questions
                        </h4>

                        <template x-for="(c,i) in chatHistory" :key="i">
                            <div class="space-y-2">
                                <p class="font-bold text-orange-400">
                                    Q: <span class="text-white" x-text="c.q"></span>
                                </p>
                                <p class="italic text-white/80">
                                    A: <span x-text="c.a"></span>
                                </p>
                            </div>
                        </template>

                        <form @submit.prevent="handleFollowUp" class="flex gap-4">
                            <input x-model="followUpQuestion" :disabled="loadingFollowUp"
                                class="flex-1 p-5 rounded-2xl bg-white/10
                  focus:outline-none focus:ring-2 focus:ring-orange-600
                  disabled:opacity-50 disabled:cursor-not-allowed"
                                placeholder="Ask something deeper..." />
                            <button :disabled="loadingFollowUp || !followUpQuestion"
                                class="bg-orange-600 px-8 rounded-2xl font-bold hover:scale-105 transition
               disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!loadingFollowUp">Ask</span>
                                <span x-show="loadingFollowUp" class="animate-pulse">Loading…</span>
                            </button>
                        </form>
                    </div>

                </div>
            </template>

        </div>

    </div>

    <!-- ALPINE COMPONENT -->
    <script>
        function eduSyncApp() {
            return {
                mode: 'Hub',
                selectedGrade: '',
                selectedSubject: '',
                selectedTopic: '',
                lesson: null,
                loading: false,
                followUpQuestion: '',
                chatHistory: [],
                loadingFollowUp: false,

                subjects: [{
                        name: 'Science',
                        icon: 'fa-microscope',
                        color: 'bg-emerald-600',
                        count: '42 Lessons'
                    },
                    {
                        name: 'Math',
                        icon: 'fa-calculator',
                        color: 'bg-blue-600',
                        count: '38 Lessons'
                    },
                    {
                        name: 'English',
                        icon: 'fa-language',
                        color: 'bg-rose-600',
                        count: '25 Lessons'
                    },
                    {
                        name: 'Social Studies',
                        icon: 'fa-landmark',
                        color: 'bg-yellow-600',
                        count: '30 Lessons'
                    },
                    {
                        name: 'Health',
                        icon: 'fa-heart-pulse',
                        color: 'bg-pink-600',
                        count: '15 Lessons'
                    },
                    {
                        name: 'Computer Science',
                        icon: 'fa-computer',
                        color: 'bg-purple-600',
                        count: '20 Lessons'
                    },
                ],

                libraryResources: {
                    3: [
                        'My Mathematics Grade 3.pdf',
                        'Nepali Grade 3.pdf',
                        'Our Serofero Grade 3.pdf'
                    ],
                    4: [
                        'English Grade 4.pdf',
                        'Mathematics Grade 4.pdf',
                        'Nepali Grade 4.pdf',
                        'Health, Physical and Creative Arts Grade 4.pdf',
                        'Science and Technology Grade 4.pdf',
                        'Social Studies and Human Value Education Grade 4.pdf'
                    ],
                    5: [
                        'English Grade 5.pdf',
                        'Mathematics Grade 5.pdf',
                        'Nepali Grade 5.pdf',
                        'Health, Physical and Creative Arts Grade 5.pdf',
                        'Science and Technology Grade 5.pdf',
                        'Social Studies Grade 5.pdf'
                    ],
                    6: [
                        'English Grade 6.pdf',
                        'My Mathematics Grade 6.pdf',
                        'Nepali Grade 6.pdf',
                        'Health, Physical and Creative Arts Grade 6.pdf',
                        'Science and Technology Grade 6.pdf',
                        'Social Studies and Human Value Education Grade 6.pdf'
                    ],
                    7: [
                        'English Grade 7.pdf',
                        'Mathematics Grade 7.pdf',
                        'Nepali Grade 7.pdf',
                        'Health, Physical and Creative Arts Grade 7.pdf',
                        'Science and Technology Grade 7.pdf',
                        'Social Studies and Human Value Education Grade 7.pdf'
                    ],
                    8: [
                        'English Grade 8.pdf',
                        'Mathematics Grade 8.pdf',
                        'Nepali Grade 8.pdf',
                        'Health, Physical and Creative Art Grade 8.pdf',
                        'Science and Technology Grade 8.pdf',
                        'Social Studies and Human Value Education Grade 8.pdf'
                    ],
                    9: [
                        'Computer Science Grade 9.pdf',
                        'English Grade 9.pdf',
                        'Mathematics Grade 9 Part 1.pdf',
                        'Mathematics Grade 9 Part 2.pdf',
                        'Nepali Grade 9.pdf',
                        'Science and Technology Grade 9.pdf',
                        'Social Studies Grade 9.pdf'
                    ],
                    10: [
                        'English Grade 10.pdf',
                        'Mathematics Class 10 Part 1.pdf',
                        'Mathematics Class 10 Part 2.pdf',
                        'Nepali Grade 10.pdf',
                        'Science and Technology Grade 10.pdf',
                        'Social Studies Grade 10.pdf'
                    ]
                },

                setMode(m) {
                    this.mode = m
                },
                setGrade(g) {
                    this.selectedGrade = g
                },

                async generateLesson() {
                    if (!this.selectedTopic) return;
                    this.loading = true;

                    const res = await fetch('/education/generate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')
                                .content
                        },
                        body: JSON.stringify({
                            grade: this.selectedGrade,
                            subject: this.selectedSubject,
                            topic: this.selectedTopic
                        })
                    });

                    const data = await res.json();
                    this.lesson = data.lesson;
                    this.loading = false;
                },

                async handleFollowUp() {
                    if (!this.followUpQuestion || !this.lesson) return;

                    const q = this.followUpQuestion;
                    this.followUpQuestion = '';
                    this.loadingFollowUp = true; // start loading

                    try {
                        const res = await fetch('/education/followup', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                context: this.lesson.content,
                                question: q
                            })
                        });

                        const data = await res.json();
                        this.chatHistory.push({
                            q,
                            a: data.answer
                        });
                    } catch (err) {
                        console.error('Error:', err);
                    } finally {
                        this.loadingFollowUp = false; // end loading
                    }
                },

                resetLesson() {
                    this.lesson = null;
                    this.chatHistory = [];
                },

                downloadResource(f) {
                    window.location.href = `/content/${encodeURIComponent(f)}`;
                }
            }
        }

        // hubAI() function
        function hubAI() {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            return {
                grade: '',
                topic: '',
                quiz: [],
                cards: [],
                loadingQuiz: false,
                loadingFlash: false,

                // Quiz state
                currentQuestion: 0,
                userAnswers: {},
                quizCompleted: false,

                // Flashcard state
                flippedCards: {},

                async loadQuiz() {
                    if (!this.grade || !this.topic) return;

                    this.loadingQuiz = true;
                    this.quiz = [];
                    this.resetQuiz();

                    try {
                        const r = await fetch('/education/quiz', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf
                            },
                            body: JSON.stringify({
                                grade: this.grade,
                                topic: this.topic
                            })
                        });

                        const data = await r.json();
                        this.quiz = data.quiz || [];
                    } catch (error) {
                        console.error('Error loading quiz:', error);
                    } finally {
                        this.loadingQuiz = false;
                    }
                },

                async loadFlash() {
                    if (!this.grade || !this.topic) return;

                    this.loadingFlash = true;
                    this.cards = [];
                    this.flippedCards = {};

                    try {
                        const r = await fetch('/education/flash', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf
                            },
                            body: JSON.stringify({
                                grade: this.grade,
                                topic: this.topic
                            })
                        });

                        const data = await r.json();
                        this.cards = data.cards || [];
                    } catch (error) {
                        console.error('Error loading flashcards:', error);
                    } finally {
                        this.loadingFlash = false;
                    }
                },

                // Quiz Methods
                selectAnswer(questionIndex, optionIndex, answer) {
                    // Prevent changing answer after selection
                    if (this.userAnswers[questionIndex] !== undefined) return;

                    this.userAnswers[questionIndex] = {
                        selectedIndex: optionIndex,
                        selectedValue: answer
                    };
                },

                nextQuestion() {
                    if (this.currentQuestion < this.quiz.length - 1) {
                        this.currentQuestion++;
                    }
                },

                previousQuestion() {
                    if (this.currentQuestion > 0) {
                        this.currentQuestion--;
                    }
                },

                completeQuiz() {
                    this.quizCompleted = true;
                },

                calculateScore() {
                    let correct = 0;
                    this.quiz.forEach((q, i) => {
                        if (this.userAnswers[i]?.selectedValue === q.answer) {
                            correct++;
                        }
                    });
                    return correct;
                },

                resetQuiz() {
                    this.currentQuestion = 0;
                    this.userAnswers = {};
                    this.quizCompleted = false;
                },

                // Flashcard Methods
                flipCard(index) {
                    this.flippedCards[index] = !this.flippedCards[index];
                },

                shuffleCards() {
                    // Fisher-Yates shuffle algorithm
                    for (let i = this.cards.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [this.cards[i], this.cards[j]] = [this.cards[j], this.cards[i]];
                    }
                    this.flippedCards = {};
                }
            }
        }
    </script>

    <style>
        .animate-reveal {
            animation: reveal .4s ease
        }

        @keyframes reveal {
            from {
                opacity: 0;
                transform: translateY(20px)
            }

            to {
                opacity: 1
            }
        }

        .rotate-y-180 {
            transform: rotateY(180deg);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <style>
        select option {
            color: black;
            background-color: white;
        }
    </style>

@endsection
