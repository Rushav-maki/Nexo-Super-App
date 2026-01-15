@extends('layouts.app')

@section('content')
<div class="flex flex-col pb-8" style="min-height: calc(100vh - 12rem); max-width: 56rem; margin: 0 auto;">
    <div class="bg-white border border-black/5 rounded-[3rem] shadow-2xl overflow-hidden flex flex-col" style="height: calc(100vh - 12rem);">
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#f5f2eb] to-[#faf8f3] p-8 border-b border-black/5 flex items-center justify-between backdrop-blur-sm">
            <div class="flex items-center gap-5 animate-fadeIn">
                <div class="h-14 w-14 rounded-full bg-gradient-to-br from-[#2a1b18] to-[#1a0f0d] border-2 border-white/20 flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-300">
                    <i class="fa-solid fa-robot text-[#c4b5fd] text-xl animate-pulse"></i>
                </div>
                <div>
                    <h3 class="font-bold text-[#2a1b18] font-serif tracking-tight text-xl uppercase">Nexo<span class="text-[#c4b5fd]">.Chat</span></h3>
                    <p class="text-[10px] text-[#4a4a4a] font-black uppercase tracking-[0.2em] italic">Cognitive Hub Active</p>
                </div>
            </div>
            <div class="flex gap-1.5 items-center animate-pulse">
                <div class="h-2 w-2 rounded-full bg-emerald-500 shadow-lg shadow-emerald-500/50"></div>
                <div class="h-2 w-2 rounded-full bg-emerald-400 opacity-75"></div>
                <div class="h-2 w-2 rounded-full bg-emerald-300 opacity-50"></div>
            </div>
        </div>

        <!-- Chat Messages Area -->
        <div x-data="chatApp()" class="flex flex-col flex-1 overflow-hidden">
            <div class="flex-1 overflow-y-auto p-6 md:p-10 space-y-6 bg-gradient-to-b from-[#faf8f3] via-[#f8f5f0] to-[#f5f2eb]"
                 id="chatMessages"
                 style="scroll-behavior: smooth;">

                <!-- Empty State -->
                <template x-if="messages.length === 0">
                    <div class="flex flex-col items-center justify-center h-full text-center space-y-8 animate-fadeIn opacity-60">
                        <div class="h-24 w-24 rounded-full border-2 border-dashed border-[#c4b5fd] flex items-center justify-center animate-pulse bg-white/50 backdrop-blur-sm">
                            <i class="fa-solid fa-feather-pointed text-5xl text-[#2a1b18]"></i>
                        </div>
                        <div class="space-y-2">
                            <p class="font-bold text-sm uppercase tracking-[0.4em] font-serif italic text-[#2a1b18]">Awaiting Matrix Command</p>
                            <p class="text-xs text-[#4a4a4a] font-medium">Ask me about Travel, Health, or Agro modules</p>
                        </div>
                    </div>
                </template>

                <!-- Messages -->
                <template x-for="(msg, index) in messages" :key="index">
                    <div
                        :class="msg.role === 'user' ? 'flex justify-end animate-slideInRight' : 'flex justify-start animate-slideInLeft'"
                        x-show="true"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0">
                        <div
                            :class="msg.role === 'user'
                                ? 'max-w-[85%] md:max-w-[75%] rounded-3xl px-6 py-4 shadow-lg bg-gradient-to-br from-[#2a1b18] to-[#1a0f0d] text-[#f8f5f0] border border-black/10 font-medium transform hover:scale-[1.02] transition-all duration-200'
                                : 'max-w-[85%] md:max-w-[75%] rounded-3xl px-6 py-4 shadow-lg bg-white text-[#2a1b18] border border-black/5 font-serif leading-relaxed backdrop-blur-sm transform hover:scale-[1.02] transition-all duration-200'">
                            <div class="flex items-start gap-3">
                                <template x-if="msg.role === 'model'">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-[#c4b5fd] to-[#a78bfa] flex items-center justify-center flex-shrink-0 mt-1">
                                        <i class="fa-solid fa-robot text-white text-xs"></i>
                                    </div>
                                </template>
                                <div class="flex-1">
                                    <div class="whitespace-pre-wrap text-sm leading-relaxed" x-html="formatMessage(msg.content)"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Loading Indicator -->
                <template x-if="loading">
                    <div class="flex justify-start animate-slideInLeft">
                        <div class="max-w-[75%] rounded-3xl px-6 py-4 shadow-lg bg-white border border-black/5 backdrop-blur-sm flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-[#c4b5fd] to-[#a78bfa] flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-robot text-white text-xs"></i>
                            </div>
                            <div class="flex gap-2">
                                <div class="w-2 h-2 bg-[#c4b5fd] rounded-full animate-bounce" style="animation-delay: 0s"></div>
                                <div class="w-2 h-2 bg-[#c4b5fd] rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                <div class="w-2 h-2 bg-[#c4b5fd] rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-ref="scrollTarget"></div>
            </div>

            <!-- Input Area -->
            <div class="p-6 md:p-8 bg-white border-t border-black/5 backdrop-blur-sm">
                <div class="flex gap-3 max-w-2xl mx-auto">
                    <div class="flex-1 relative">
                        <input
                            type="text"
                            x-model="input"
                            @keydown.enter.prevent="handleSend()"
                            placeholder="Query the system..."
                            class="w-full rounded-2xl bg-gradient-to-r from-[#f5f2eb] to-[#faf8f3] border-2 border-black/10 text-[#2a1b18] px-6 py-4 focus:ring-2 focus:ring-[#c4b5fd] focus:border-[#c4b5fd] transition-all duration-200 font-medium italic placeholder:text-black/30 shadow-sm focus:shadow-md"
                        />
                        <div x-show="input.length > 0" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <span class="text-xs text-[#4a4a4a] font-bold" x-text="input.length + '/1000'"></span>
                        </div>
                    </div>
                    <button
                        @click="handleSend()"
                        :disabled="loading || !input.trim()"
                        class="bg-gradient-to-br from-[#2a1b18] to-[#1a0f0d] text-white rounded-2xl px-8 py-4 font-black hover:from-[#3a2b28] hover:to-[#2a1f1d] disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 active:scale-95"
                    >
                        <i class="fa-solid fa-location-arrow transform rotate-[-45deg]"></i>
                    </button>
                </div>
                <p class="text-xs text-center text-[#4a4a4a] mt-3 opacity-60">
                    Press Enter to send • Nexa.Chat is context-aware
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    window.chatApp = function() {
        return {
            messages: [],
            input: '',
            loading: false,

            formatMessage(text) {
                // Simple text formatting - escape HTML and preserve line breaks
                return text
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/\n/g, '<br>');
            },

            async handleSend() {
                if (!this.input.trim() || this.loading) return;

                // Limit message length
                if (this.input.length > 1000) {
                    alert('Message is too long. Please limit to 1000 characters.');
                    return;
                }

                const userMessage = this.input.trim();
                this.messages.push({ role: 'user', content: userMessage });
                this.input = '';
                this.loading = true;

                // Scroll to bottom immediately
                this.$nextTick(() => {
                    this.scrollToBottom();
                });

                try {
                    // Prepare messages for API - only send user and model messages
                    const apiMessages = this.messages
                        .filter(msg => msg.role === 'user' || msg.role === 'model')
                        .map(msg => ({
                            role: msg.role === 'model' ? 'assistant' : msg.role,
                            content: msg.content
                        }));

                    const response = await fetch("{{ route('user.chat.send') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            message: userMessage,
                            messages: apiMessages.slice(0, -1) // Send all except the current user message (it's added in controller)
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.status === 'success') {
                        this.messages.push({ role: 'model', content: data.message });
                    } else {
                        throw new Error(data.message || 'Failed to get response');
                    }
                } catch (err) {
                    console.error('Chat error:', err);
                    this.messages.push({
                        role: 'model',
                        content: 'Sorry, I encountered an error processing your message. Please try again in a moment.'
                    });
                } finally {
                    this.loading = false;
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                }
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const chatMessages = document.getElementById('chatMessages');
                    if (chatMessages) {
                        chatMessages.scrollTo({
                            top: chatMessages.scrollHeight,
                            behavior: 'smooth'
                        });
                    }
                    if (this.$refs.scrollTarget) {
                        this.$refs.scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                });
            }
        }
    }
</script>

<style>
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-8px);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-bounce {
        animation: bounce 1.4s ease-in-out infinite;
    }

    .animate-fadeIn {
        animation: fadeIn 0.5s ease-in;
    }

    .animate-slideInLeft {
        animation: slideInLeft 0.4s ease-out;
    }

    .animate-slideInRight {
        animation: slideInRight 0.4s ease-out;
    }

    /* Custom Scrollbar */
    #chatMessages::-webkit-scrollbar {
        width: 8px;
    }

    #chatMessages::-webkit-scrollbar-track {
        background: transparent;
    }

    #chatMessages::-webkit-scrollbar-thumb {
        background: rgba(196, 181, 253, 0.3);
        border-radius: 10px;
    }

    #chatMessages::-webkit-scrollbar-thumb:hover {
        background: rgba(196, 181, 253, 0.5);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .flex.items-start.gap-3 {
            flex-direction: column;
        }
    }
</style>
@endsection
