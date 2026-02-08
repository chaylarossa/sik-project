@props(['value' => 0])

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    <div class="flex items-center justify-between mb-1">
        <span class="text-xs font-medium text-gray-700">Progres</span>
        <span class="text-xs font-bold text-gray-700">{{ $value }}%</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-200 overflow-hidden">
        <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500 ease-out" style="width: {{ $value }}%"></div>
    </div>
</div>