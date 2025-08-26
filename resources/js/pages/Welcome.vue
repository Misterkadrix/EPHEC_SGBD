<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();
</script>

<template>
    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
            <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="font-semibold text-gray-900 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                Dashboard
            </Link>

            <template v-else>
                <Link :href="route('login')" class="font-semibold text-gray-900 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                    Log in
                </Link>

                <Link v-if="canRegister" :href="route('register')" class="ml-4 font-semibold text-gray-900 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                    Register
                </Link>
            </template>
        </div>

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="flex justify-center">
                <svg viewBox="0 0 62 10" xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 dark:hidden">
                    <path d="M6.5 0c-.8 0-1.5.7-1.5 1.5v7C5 9.3 5.7 10 6.5 10h49c.8 0 1.5-.7 1.5-1.5v-7C57 .7 56.3 0 55.5 0h-49zM6 1.5c0-.3.2-.5.5-.5h49c.3 0 .5.2.5.5v7c0 .3-.2.5-.5.5h-49c-.3 0-.5-.2-.5-.5v-7zM16 3h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3z" fill="#F59E0B"/>
                    <path d="M0 3h4v4H0V3zm6 0h4v4H6V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3z" fill="#EF4444"/>
                </svg>
                <svg viewBox="0 0 62 10" xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 hidden dark:block">
                    <path d="M6.5 0c-.8 0-1.5.7-1.5 1.5v7C5 9.3 5.7 10 6.5 10h49c.8 0 1.5-.7 1.5-1.5v-7C57 .7 56.3 0 55.5 0h-49zM6 1.5c0-.3.2-.5.5-.5h49c.3 0 .5.2.5.5v7c0 .3-.2.5-.5.5h-49c-.3 0-.5-.2-.5-.5v-7zM16 3h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3z" fill="#F59E0B"/>
                    <path d="M0 3h4v4H0V3zm6 0h4v4H6V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3zm6 0h4v4h-4V3z" fill="#EF4444"/>
                </svg>
            </div>

            <div class="mt-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                    <div class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-black/80 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-8 h-8 stroke-gray-800 dark:stroke-white"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                                <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">
                                    <a href="https://github.com/spatie/laravel-ignition" class="rounded-lg focus:outline focus:outline-2 focus:outline-red-500 focus:outline-offset-2">
                                        Ignition
                                    </a>
                                </h2>
                            </div>

                            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Laravel Ignition is a beautiful error page for Laravel applications. You can open this template in
                                <code class="bg-gray-800/50 rounded px-1 py-0.5 text-white dark:text-white">resources/views/ignition-error.blade.php</code>
                                to customize it.
                            </p>
                        </div>
                    </div>

                    <div class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-black/80 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-8 h-8 stroke-gray-800 dark:stroke-white"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                                <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">
                                    <a href="https://github.com/spatie/laravel-ignition" class="rounded-lg focus:outline focus:outline-2 focus:outline-red-500 focus:outline-offset-2">
                                        Ignition
                                    </a>
                                </h2>
                            </div>

                            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Laravel Ignition is a beautiful error page for Laravel applications. You can open this template in
                                <code class="bg-gray-800/50 rounded px-1 py-0.5 text-white dark:text-white">resources/views/ignition-error.blade.php</code>
                                to customize it.
                            </p>
                        </div>
                    </div>

                    <div class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-black/80 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-8 h-8 stroke-gray-800 dark:stroke-white"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                                <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">
                                    <a href="https://github.com/spatie/laravel-ignition" class="rounded-lg focus:outline focus:outline-2 focus:outline-red-500 focus:outline-offset-2">
                                        Ignition
                                    </a>
                                </h2>
                            </div>

                            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Laravel Ignition is a beautiful error page for Laravel applications. You can open this template in
                                <code class="bg-gray-800/50 rounded px-1 py-0.5 text-white dark:text-white">resources/views/ignition-error.blade.php</code>
                                to customize it.
                            </p>
                        </div>
                    </div>

                    <div class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-black/80 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-8 h-8 stroke-gray-800 dark:stroke-white"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                                <h2 class="ml-3 text-xl font-semibold text-gray-900 dark:text-white">
                                    <a href="https://github.com/spatie/laravel-ignition" class="rounded-lg focus:outline focus:outline-2 focus:outline-red-500 focus:outline-offset-2">
                                        Ignition
                                    </a>
                                </h2>
                            </div>

                            <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                Laravel Ignition is a beautiful error page for Laravel applications. You can open this template in
                                <code class="bg-gray-800/50 rounded px-1 py-0.5 text-white dark:text-white">resources/views/ignition-error.blade.php</code>
                                to customize it.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-16 px-6 sm:items-center sm:justify-between">
                <div class="text-center text-sm text-gray-500 dark:text-gray-400 sm:text-start">
                    <div class="flex items-center gap-4">
                        <a href="https://github.com/spatie/laravel-ignition" class="group inline-flex items-center hover:text-gray-700 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="-mt-px mr-1 w-5 h-5 stroke-gray-400 dark:stroke-gray-600 group-hover:stroke-gray-600 dark:group-hover:stroke-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                            Laravel
                        </a>

                        <div class="border-l border-gray-200 dark:border-gray-700 mx-4 h-4"></div>

                        <a href="https://github.com/spatie/laravel-ignition" class="group inline-flex items-center hover:text-gray-700 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="-mt-px mr-1 w-5 h-5 stroke-gray-400 dark:stroke-gray-600 group-hover:stroke-gray-600 dark:group-hover:stroke-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                            Laravel
                        </a>
                    </div>
                </div>

                <div class="ml-4 text-center text-sm text-gray-500 dark:text-gray-400 sm:text-right sm:ml-0">
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23000000' fill-opacity='0.1'%3E%3Ccircle cx='2' cy='2' r='1'/%3E%3C/g%3E%3C/svg%3E");
}

@media (prefers-color-scheme: dark) {
    .bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='2' cy='2' r='1'/%3E%3C/g%3E%3C/svg%3E");
    }
}
</style>
