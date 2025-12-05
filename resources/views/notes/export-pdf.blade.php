<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $note->title }} - Ringkesin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #1E293B;
            background: white;
            padding: 40px;
        }

        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2C74B3;
        }

        .title {
            font-size: 28px;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 15px;
        }

        .metadata {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 14px;
            color: #64748B;
        }

        .metadata-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .category-badge {
            display: inline-block;
            padding: 5px 12px;
            background: #EFF6FF;
            color: #2C74B3;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
        }

        .tags {
            margin-top: 15px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .tag {
            padding: 4px 10px;
            background: #F1F5F9;
            color: #475569;
            border-radius: 6px;
            font-size: 12px;
        }

        .content {
            margin-top: 30px;
            font-size: 16px;
            line-height: 1.8;
        }

        .content h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 30px 0 15px;
            color: #0F172A;
            border-bottom: 2px solid #E2E8F0;
            padding-bottom: 8px;
        }

        .content h2 {
            font-size: 20px;
            font-weight: 600;
            margin: 25px 0 12px;
            color: #1E293B;
        }

        .content h3 {
            font-size: 18px;
            font-weight: 600;
            margin: 20px 0 10px;
            color: #334155;
        }

        .content p {
            margin-bottom: 16px;
        }

        .content ul, .content ol {
            margin: 15px 0;
            padding-left: 25px;
        }

        .content li {
            margin-bottom: 8px;
        }

        .content strong {
            font-weight: 700;
            color: #0F172A;
        }

        .content em {
            font-style: italic;
        }

        .content code {
            background: #F1F5F9;
            color: #2C74B3;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }

        .content pre {
            background: #1E293B;
            color: #F8FAFC;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            margin: 20px 0;
        }

        .content pre code {
            background: transparent;
            color: inherit;
            padding: 0;
        }

        .content blockquote {
            border-left: 4px solid #2C74B3;
            padding-left: 15px;
            margin: 20px 0;
            font-style: italic;
            color: #475569;
            background: #F8FAFC;
            padding: 12px 15px;
        }

        .content table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .content th,
        .content td {
            border: 1px solid #E2E8F0;
            padding: 10px;
            text-align: left;
        }

        .content th {
            background: #F1F5F9;
            font-weight: 600;
        }

        .content img {
            max-width: 100%;
            height: auto;
            margin: 20px 0;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #E2E8F0;
            font-size: 12px;
            color: #94A3B8;
            text-align: center;
        }

        @media print {
            body {
                padding: 20px;
            }
            
            .no-print {
                display: none !important;
            }

            @page {
                margin: 1cm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $note->title }}</div>
        <div class="metadata">
            @if($note->category)
                <span class="category-badge">
                    {{ $note->category->icon }} {{ $note->category->name }}
                </span>
            @endif
            <span class="metadata-item">
                📅 {{ $note->created_at->format('d M Y') }}
            </span>
            <span class="metadata-item">
                🕐 {{ $note->created_at->format('H:i') }}
            </span>
            <span class="metadata-item">
                👤 {{ $note->user->name }}
            </span>
        </div>

        @if($note->tags->count() > 0)
            <div class="tags">
                @foreach($note->tags as $tag)
                    <span class="tag">#{{ $tag->name }}</span>
                @endforeach
            </div>
        @endif
    </div>

    <div class="content">
        {!! format_note_content($note->content) !!}
    </div>

    <div class="footer">
        <p>Generated from Ringkesin - {{ now()->format('d F Y, H:i') }}</p>
        <p>{{ config('app.url') }}</p>
    </div>

    <script>
        // Auto-trigger print dialog when page loads
        window.onload = function() {
            window.print();
        };

        // Close window after printing (optional)
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>
