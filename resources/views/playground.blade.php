<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumen Content Negotiation — API Studio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="h-full flex flex-col font-sans antialiased selection:bg-indigo-500 selection:text-white">

    <!-- Top Navigation Header -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-amber-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <i data-lucide="layers" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-lg text-white tracking-tight">Lumen API Studio</span>
                        <span class="text-[11px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Content Negotiation</span>
                    </div>
                    <p class="text-xs text-slate-400">PHP Lumen &bull; Accept & Content-Type Negotiation Playground</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="#reference" class="text-xs text-slate-400 hover:text-white transition flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-slate-800">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                    <span>Panduan PDF</span>
                </a>
                <span class="w-px h-5 bg-slate-800"></span>
                <span class="inline-flex items-center gap-1.5 text-xs text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    API Active
                </span>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Request Builder & Post List (7 cols) -->
        <section class="lg:col-span-7 flex flex-col gap-6">

            <!-- Request Composer Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="send" class="w-4 h-4 text-indigo-400"></i>
                        <h2 class="text-sm font-semibold text-white tracking-wide uppercase">Request Composer</h2>
                    </div>
                    <span class="text-xs text-slate-400">Uji Header & Endpoint secara live</span>
                </div>

                <!-- Method + URL Input Bar -->
                <div class="flex flex-col sm:flex-row gap-2 mb-4">
                    <select id="httpMethod" class="bg-slate-800 text-slate-100 font-mono text-xs font-bold rounded-xl px-3 py-2.5 border border-slate-700 focus:outline-none focus:border-indigo-500 cursor-pointer">
                        <option value="GET" selected>GET</option>
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="DELETE">DELETE</option>
                    </select>

                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 font-mono text-xs">
                            /
                        </span>
                        <input id="endpointInput" type="text" value="posts" class="w-full bg-slate-800 text-slate-100 font-mono text-xs rounded-xl pl-6 pr-3 py-2.5 border border-slate-700 focus:outline-none focus:border-indigo-500" placeholder="posts atau post/{id}">
                    </div>

                    <button id="sendBtn" onclick="executeRequest()" class="bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] text-white font-medium text-xs px-5 py-2.5 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/30">
                        <i data-lucide="play" class="w-3.5 h-3.5 fill-current"></i>
                        <span>Kirim Request</span>
                    </button>
                </div>

                <!-- Quick Presets -->
                <div class="flex flex-wrap items-center gap-2 mb-5 pb-4 border-b border-slate-800/80">
                    <span class="text-[11px] font-medium text-slate-400">Presets:</span>
                    <button onclick="applyPreset('readAllJson')" class="text-[11px] px-2.5 py-1 rounded-lg bg-slate-800/60 hover:bg-slate-800 text-slate-300 border border-slate-700/50 hover:border-slate-600 transition">GET /posts (JSON)</button>
                    <button onclick="applyPreset('readAllXml')" class="text-[11px] px-2.5 py-1 rounded-lg bg-slate-800/60 hover:bg-slate-800 text-slate-300 border border-slate-700/50 hover:border-slate-600 transition">GET /posts (XML)</button>
                    <button onclick="applyPreset('noAccept406')" class="text-[11px] px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-300 border border-rose-500/20 hover:bg-rose-500/20 transition">Simulasi 406 (Tanpa Accept)</button>
                    <button onclick="applyPreset('noContentType415')" class="text-[11px] px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-300 border border-amber-500/20 hover:bg-amber-500/20 transition">Simulasi 415 (Tanpa Content-Type)</button>
                </div>

                <!-- Headers Configuration Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Accept Header Picker -->
                    <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800">
                        <label class="block text-xs font-semibold text-slate-300 mb-2 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="tag" class="w-3.5 h-3.5 text-indigo-400"></i>
                                Header <code>Accept</code>
                            </span>
                            <span class="text-[10px] text-slate-500">Format Response</span>
                        </label>
                        <div class="space-y-1.5 text-xs">
                            <label class="flex items-center gap-2 text-slate-300 cursor-pointer p-1.5 rounded-lg hover:bg-slate-800/50">
                                <input type="radio" name="acceptHeader" value="application/json" checked onchange="updateHeaderPreview()" class="text-indigo-600 focus:ring-0 bg-slate-800 border-slate-700">
                                <span class="font-mono text-emerald-400">application/json</span>
                            </label>
                            <label class="flex items-center gap-2 text-slate-300 cursor-pointer p-1.5 rounded-lg hover:bg-slate-800/50">
                                <input type="radio" name="acceptHeader" value="application/xml" onchange="updateHeaderPreview()" class="text-indigo-600 focus:ring-0 bg-slate-800 border-slate-700">
                                <span class="font-mono text-cyan-400">application/xml</span>
                            </label>
                            <label class="flex items-center gap-2 text-slate-400 cursor-pointer p-1.5 rounded-lg hover:bg-slate-800/50">
                                <input type="radio" name="acceptHeader" value="NONE" onchange="updateHeaderPreview()" class="text-indigo-600 focus:ring-0 bg-slate-800 border-slate-700">
                                <span class="text-rose-400 italic">Kosongkan (Tanpa Header Accept)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Content-Type Header Picker -->
                    <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800">
                        <label class="block text-xs font-semibold text-slate-300 mb-2 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="file-code" class="w-3.5 h-3.5 text-amber-400"></i>
                                Header <code>Content-Type</code>
                            </span>
                            <span class="text-[10px] text-slate-500">Untuk POST & PUT</span>
                        </label>
                        <div class="space-y-1.5 text-xs">
                            <label class="flex items-center gap-2 text-slate-300 cursor-pointer p-1.5 rounded-lg hover:bg-slate-800/50">
                                <input type="radio" name="contentTypeHeader" value="application/json" checked onchange="updateHeaderPreview()" class="text-indigo-600 focus:ring-0 bg-slate-800 border-slate-700">
                                <span class="font-mono text-emerald-400">application/json</span>
                            </label>
                            <label class="flex items-center gap-2 text-slate-400 cursor-pointer p-1.5 rounded-lg hover:bg-slate-800/50">
                                <input type="radio" name="contentTypeHeader" value="NONE" onchange="updateHeaderPreview()" class="text-indigo-600 focus:ring-0 bg-slate-800 border-slate-700">
                                <span class="text-amber-400 italic">Kosongkan (Tanpa Content-Type)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Request Body (JSON) -->
                <div id="requestBodyContainer" class="mb-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>Request Body (JSON)</span>
                        <span class="text-[10px] text-slate-500">Kirim saat POST / PUT</span>
                    </label>
                    <textarea id="requestBodyInput" rows="4" class="w-full bg-slate-950 text-slate-200 font-mono text-xs rounded-xl p-3 border border-slate-800 focus:outline-none focus:border-indigo-500 resize-none" placeholder='{"title": "Judul Baru", "content": "Konten postingan...", "user_id": 1}'>{
  "title": "Postingan Baru dari Studio",
  "content": "Ini adalah pengujian otomatis Content Negotiation menggunakan Lumen.",
  "user_id": 1
}</textarea>
                </div>
            </div>

            <!-- Posts List Visual Explorer -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl flex-1 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="database" class="w-4 h-4 text-emerald-400"></i>
                        <h2 class="text-sm font-semibold text-white tracking-wide uppercase">Database Posts Explorer</h2>
                    </div>
                    <button onclick="loadPostsList()" class="text-xs text-indigo-400 hover:text-indigo-300 transition flex items-center gap-1">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                        <span>Muat Ulang</span>
                    </button>
                </div>

                <div id="postsListCards" class="space-y-3 overflow-y-auto max-h-96 pr-1">
                    <div class="p-6 text-center text-slate-500 text-xs">Memuat data post...</div>
                </div>
            </div>
        </section>

        <!-- Right Column: Response Inspector & PDF Reference (5 cols) -->
        <section class="lg:col-span-5 flex flex-col gap-6">

            <!-- Response Inspector Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl flex-1 flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="terminal" class="w-4 h-4 text-cyan-400"></i>
                        <h2 class="text-sm font-semibold text-white tracking-wide uppercase">Response Inspector</h2>
                    </div>
                    <span id="timingBadge" class="text-[11px] font-mono text-slate-400">0 ms</span>
                </div>

                <!-- Status & Content-Type Pills -->
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <div id="statusPill" class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-800 text-slate-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                        <span id="statusCodeText">Belum ada request</span>
                    </div>

                    <div id="formatPill" class="px-2.5 py-1 rounded-lg text-xs font-mono bg-slate-800 text-slate-400">
                        Content-Type: <span id="contentTypeText">-</span>
                    </div>
                </div>

                <!-- Response Body Display with Format Tabs -->
                <div class="relative flex-1 flex flex-col min-h-[300px] bg-slate-950 border border-slate-800/80 rounded-xl overflow-hidden">
                    <div class="bg-slate-900/90 px-3 py-1.5 border-b border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
                        <span class="font-mono text-slate-300">Payload Output</span>
                        <button onclick="copyResponsePayload()" class="hover:text-white transition flex items-center gap-1">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                            <span id="copyBtnText">Salin</span>
                        </button>
                    </div>

                    <pre id="responsePayload" class="flex-1 p-4 font-mono text-xs text-slate-200 overflow-auto whitespace-pre-wrap break-all leading-relaxed">Tekan tombol "Kirim Request" atau klik salah satu preset untuk menguji response API...</pre>
                </div>
            </div>

            <!-- PDF Quick Reference Card -->
            <div id="reference" class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-indigo-400"></i>
                    <h3 class="text-xs font-semibold text-white uppercase tracking-wider">Aturan Materi PDF</h3>
                </div>

                <div class="space-y-2 text-xs text-slate-400">
                    <div class="flex items-start gap-2">
                        <span class="text-emerald-400 font-bold">&bull;</span>
                        <p><strong class="text-slate-200">Accept:</strong> Hanya menerima <code class="text-emerald-400 bg-slate-800 px-1 py-0.5 rounded">application/json</code> atau <code class="text-cyan-400 bg-slate-800 px-1 py-0.5 rounded">application/xml</code>. Selain itu $\rightarrow$ <span class="text-rose-400 font-semibold">406 Not Acceptable!</span></p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-amber-400 font-bold">&bull;</span>
                        <p><strong class="text-slate-200">Content-Type (POST/PUT):</strong> Wajib <code class="text-emerald-400 bg-slate-800 px-1 py-0.5 rounded">application/json</code>. Selain itu $\rightarrow$ <span class="text-amber-400 font-semibold">415 Unsupported Media Type</span></p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-indigo-400 font-bold">&bull;</span>
                        <p><strong class="text-slate-200">SimpleXMLElement:</strong> Digunakan untuk merender struktur XML saat header <code>Accept: application/xml</code> dipenuhi.</p>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Client-side Logic -->
    <script>
        lucide.createIcons();

        function getHeaders() {
            const acceptVal = document.querySelector('input[name="acceptHeader"]:checked').value;
            const contentVal = document.querySelector('input[name="contentTypeHeader"]:checked').value;

            const headers = {};
            if (acceptVal !== 'NONE') {
                headers['Accept'] = acceptVal;
            }
            if (contentVal !== 'NONE') {
                headers['Content-Type'] = contentVal;
            }
            return headers;
        }

        function updateHeaderPreview() {
            // Visual feedback if needed
        }

        async function executeRequest() {
            const method = document.getElementById('httpMethod').value;
            let endpoint = document.getElementById('endpointInput').value.trim();
            if (endpoint.startsWith('/')) endpoint = endpoint.substring(1);

            const url = '/' + endpoint;
            const headers = getHeaders();

            const options = {
                method: method,
                headers: headers
            };

            if (['POST', 'PUT'].includes(method)) {
                options.body = document.getElementById('requestBodyInput').value;
            }

            const sendBtn = document.getElementById('sendBtn');
            sendBtn.disabled = true;
            sendBtn.classList.add('opacity-70');

            const startTime = performance.now();
            try {
                const response = await fetch(url, options);
                const duration = Math.round(performance.now() - startTime);

                document.getElementById('timingBadge').innerText = duration + ' ms';
                const status = response.status;
                const statusText = response.statusText || '';
                const contentType = response.headers.get('content-type') || 'none';

                // Status Pill styling
                const statusPill = document.getElementById('statusPill');
                const statusCodeText = document.getElementById('statusCodeText');
                statusCodeText.innerText = `${status} ${statusText}`;

                if (status >= 200 && status < 300) {
                    statusPill.className = "px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center gap-1.5";
                } else if (status === 406) {
                    statusPill.className = "px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20 flex items-center gap-1.5";
                } else if (status === 415) {
                    statusPill.className = "px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center gap-1.5";
                } else {
                    statusPill.className = "px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-800 text-slate-300 flex items-center gap-1.5";
                }

                document.getElementById('contentTypeText').innerText = contentType;

                const text = await response.text();
                const payloadEl = document.getElementById('responsePayload');

                if (contentType.includes('application/json')) {
                    try {
                        const parsed = JSON.parse(text);
                        payloadEl.innerText = JSON.stringify(parsed, null, 2);
                    } catch(e) {
                        payloadEl.innerText = text;
                    }
                } else {
                    payloadEl.innerText = text;
                }

                // If mutation succeeded, refresh list
                if (['POST', 'PUT', 'DELETE'].includes(method) && status < 300) {
                    loadPostsList();
                }

            } catch (err) {
                document.getElementById('statusCodeText').innerText = 'Network Error';
                document.getElementById('responsePayload').innerText = err.message;
            } finally {
                sendBtn.disabled = false;
                sendBtn.classList.remove('opacity-70');
            }
        }

        async function loadPostsList() {
            const container = document.getElementById('postsListCards');
            container.innerHTML = '<div class="p-6 text-center text-slate-500 text-xs">Memuat data post...</div>';

            try {
                const res = await fetch('/posts', {
                    headers: { 'Accept': 'application/json' }
                });

                if (!res.ok) {
                    container.innerHTML = `<div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-400 text-xs">Gagal memuat list: Status ${res.status}</div>`;
                    return;
                }

                const data = await res.json();
                const items = data.results?.data || data.results || [];

                if (items.length === 0) {
                    container.innerHTML = '<div class="p-6 text-center text-slate-500 text-xs">Belum ada post di database. Silakan buat post baru!</div>';
                    return;
                }

                container.innerHTML = items.map(post => `
                    <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800/80 hover:border-slate-700 transition flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-white truncate max-w-[240px]">#${post.id} ${escapeHtml(post.title)}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-400 font-mono">${escapeHtml(post.status || 'published')}</span>
                        </div>
                        <p class="text-xs text-slate-400 line-clamp-2">${escapeHtml(post.content || '')}</p>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-900 text-[11px]">
                            <span class="text-slate-500 font-mono">User ID: ${post.user_id}</span>
                            <div class="flex items-center gap-1.5">
                                <button onclick="quickInspect(${post.id}, 'application/json')" class="px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500/20 font-mono">JSON</button>
                                <button onclick="quickInspect(${post.id}, 'application/xml')" class="px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 font-mono">XML</button>
                                <button onclick="quickDelete(${post.id})" class="px-2 py-0.5 rounded bg-rose-500/10 text-rose-400 hover:bg-rose-500/20">Hapus</button>
                            </div>
                        </div>
                    </div>
                `).join('');

            } catch (err) {
                container.innerHTML = `<div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-400 text-xs">Error: ${err.message}</div>`;
            }
        }

        function applyPreset(type) {
            if (type === 'readAllJson') {
                document.getElementById('httpMethod').value = 'GET';
                document.getElementById('endpointInput').value = 'posts';
                document.querySelector('input[name="acceptHeader"][value="application/json"]').checked = true;
            } else if (type === 'readAllXml') {
                document.getElementById('httpMethod').value = 'GET';
                document.getElementById('endpointInput').value = 'posts';
                document.querySelector('input[name="acceptHeader"][value="application/xml"]').checked = true;
            } else if (type === 'noAccept406') {
                document.getElementById('httpMethod').value = 'GET';
                document.getElementById('endpointInput').value = 'posts';
                document.querySelector('input[name="acceptHeader"][value="NONE"]').checked = true;
            } else if (type === 'noContentType415') {
                document.getElementById('httpMethod').value = 'POST';
                document.getElementById('endpointInput').value = 'posts';
                document.querySelector('input[name="acceptHeader"][value="application/json"]').checked = true;
                document.querySelector('input[name="contentTypeHeader"][value="NONE"]').checked = true;
            }
            executeRequest();
        }

        function quickInspect(id, format) {
            document.getElementById('httpMethod').value = 'GET';
            document.getElementById('endpointInput').value = `post/${id}`;
            document.querySelector(`input[name="acceptHeader"][value="${format}"]`).checked = true;
            executeRequest();
        }

        function quickDelete(id) {
            if (!confirm(`Hapus post #${id}?`)) return;
            document.getElementById('httpMethod').value = 'DELETE';
            document.getElementById('endpointInput').value = `post/${id}`;
            document.querySelector('input[name="acceptHeader"][value="application/json"]').checked = true;
            executeRequest();
        }

        function copyResponsePayload() {
            const text = document.getElementById('responsePayload').innerText;
            navigator.clipboard.writeText(text);
            const btn = document.getElementById('copyBtnText');
            btn.innerText = 'Tersalin!';
            setTimeout(() => { btn.innerText = 'Salin'; }, 1500);
        }

        function escapeHtml(str) {
            return String(str).replace(/[&<>"']/g, function(m) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadPostsList();
        });
    </script>
</body>
</html>
