<!DOCTYPE html>
<html lang="en"
      x-data="{ dark: (localStorage.getItem('config-explorer-dark') ?? 'true') === 'true' }"
      x-init="$watch('dark', v => localStorage.setItem('config-explorer-dark', v))"
      :class="{ 'dark': dark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Config Explorer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 antialiased">
    <div x-data="explorer(@js($entries), @js($groups))" x-cloak class="min-h-screen">
        <header class="border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold tracking-tight">Config Explorer</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Laravel {{ $laravelVersion }} &middot; PHP {{ $phpVersion }} &middot; env: <span class="font-medium">{{ $environment }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-500 dark:text-slate-400 tabular-nums"
                          x-text="`${filtered.length} / ${all.length} keys`"></span>
                    <button type="button"
                            @click="dark = !dark"
                            class="rounded-md border border-slate-300 dark:border-slate-700 px-3 py-1.5 text-xs font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <span x-show="!dark">Dark</span>
                        <span x-show="dark">Light</span>
                    </button>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-6 pb-4 flex flex-col sm:flex-row gap-3">
                <input type="search"
                       x-model="search"
                       autofocus
                       placeholder="Search keys or values (e.g. database, redis, queue.connections)"
                       class="flex-1 rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <select x-model="group"
                        class="rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm">
                    <option value="">All groups</option>
                    <template x-for="g in groups" :key="g">
                        <option :value="g" x-text="g"></option>
                    </template>
                </select>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 py-6">
            <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <table class="w-full text-sm">
                    <thead class="bg-slate-100 dark:bg-slate-800/60 text-left text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-2.5 font-medium w-1/3">Key</th>
                            <th class="px-4 py-2.5 font-medium w-24">Type</th>
                            <th class="px-4 py-2.5 font-medium">Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <template x-for="entry in filtered" :key="entry.key">
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td class="px-4 py-2 font-mono text-xs break-all align-top">
                                    <span x-text="entry.key"></span>
                                </td>
                                <td class="px-4 py-2 align-top">
                                    <span class="inline-block rounded bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 text-[10px] font-medium text-slate-600 dark:text-slate-400"
                                          x-text="entry.type"></span>
                                </td>
                                <td class="px-4 py-2 font-mono text-xs break-all align-top">
                                    <template x-if="entry.redacted">
                                        <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                                            [REDACTED]
                                        </span>
                                    </template>
                                    <template x-if="!entry.redacted">
                                        <span x-text="entry.value"></span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filtered.length === 0">
                            <td colspan="3" class="px-4 py-12 text-center text-slate-500 dark:text-slate-400 text-sm">
                                No matching configuration entries.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-[11px] text-slate-400 dark:text-slate-600 mt-3 text-center">
                Configuration is rendered from <code class="font-mono">config()-&gt;all()</code>. Sensitive values are redacted client-side via the
                <code class="font-mono">redact_patterns</code> config. Do not expose this route in production.
            </p>
        </main>
    </div>

    <script>
        function explorer(entries, groupList) {
            return {
                all: entries,
                groups: groupList,
                search: '',
                group: '',
                get filtered() {
                    const term = this.search.trim().toLowerCase();
                    const group = this.group;
                    return this.all.filter(e => {
                        if (group && e.group !== group) return false;
                        if (!term) return true;
                        return e.key.toLowerCase().includes(term) ||
                               (typeof e.value === 'string' && e.value.toLowerCase().includes(term));
                    });
                },
            };
        }
    </script>
</body>
</html>
