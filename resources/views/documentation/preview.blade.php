<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Просмотр') }} — {{ $document->title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { margin: 0; padding: 0; }
        iframe { width: 100%; height: 100vh; border: none; }
    </style>
</head>
<body>
    {{--
        Office Web Viewer принимает параметр src — полный URL к файлу.
        Мы передаём временную signed-ссылку, которая действительна 5 минут.

        Ограничение: Office Web Viewer НЕ работает на localhost.
        Для локальной разработки предпросмотр DOCX/XLSX будет недоступен.

        Документация: https://learn.microsoft.com/en-us/office/office-web-viewer
    --}}
    <iframe
        src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($signedUrl) }}"
        sandbox="allow-scripts allow-same-origin allow-popups allow-forms"
        title="{{ $document->title }}">
    </iframe>
</body>
</html>
