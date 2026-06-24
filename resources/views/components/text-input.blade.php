@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-cyan-500 focus:ring-indigo-500 dark:focus:ring-cyan-500 rounded-md shadow-sm']) }}>
