<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-cyan-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-cyan-500 focus:bg-gray-700 dark:focus:bg-cyan-500 active:bg-gray-900 dark:active:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-cyan-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
