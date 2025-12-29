<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $note->title }}</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 2.54cm; /* Margin normal Word (1 inch) */
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5; /* Line spacing 1.5 */
            color: #000000;
            background: white;
        }

        /* Header Dokumen - Sederhana */
        .document-header {
            text-align: center;
            margin-bottom: 18pt;
        }

        .document-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 6pt;
        }

        .document-subtitle {
            font-size: 12pt;
            font-weight: normal;
            margin-bottom: 18pt;
        }

        /* Konten Utama */
        .content {
            text-align: justify;
        }

        /* Heading Styles */
        .content h1 {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 12pt;
            margin-bottom: 0;
        }

        .content h2 {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 12pt;
            margin-bottom: 0;
        }

        .content h3 {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 12pt;
            margin-bottom: 0;
        }

        .content h4 {
            font-size: 12pt;
            font-weight: bold;
            font-style: italic;
            margin-top: 12pt;
            margin-bottom: 0;
        }

        /* Paragraf */
        .content p {
            margin-top: 0;
            margin-bottom: 0;
        }

        /* Lists - Tanpa bullet CSS (pakai teks biasa untuk kompatibilitas Word) */
        .content ul, .content ol {
            margin: 0;
            padding-left: 0;
            list-style: none;
        }

        .content li {
            margin-bottom: 0;
            padding-left: 36pt;
            text-indent: -18pt;
        }

        .content ul > li::before {
            content: "• ";
            font-family: 'Times New Roman', Times, serif;
        }

        .content ol {
            counter-reset: list-counter;
        }

        .content ol > li {
            counter-increment: list-counter;
        }

        .content ol > li::before {
            content: counter(list-counter) ". ";
        }

        /* Nested lists */
        .content ul ul > li::before {
            content: "- ";
        }

        .content ul ul ul > li::before {
            content: "○ ";
        }

        /* Bold & Italic */
        .content strong, .content b {
            font-weight: bold;
        }

        .content em, .content i {
            font-style: italic;
        }

        /* Code */
        .content code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11pt;
        }

        .content pre {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11pt;
            margin: 12pt 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .content pre code {
            background: none;
        }

        /* Blockquote */
        .content blockquote {
            margin: 12pt 0 12pt 36pt;
            font-style: italic;
        }

        .content blockquote p {
            margin-bottom: 0;
        }

        /* Tables */
        .content table {
            width: 100%;
            border-collapse: collapse;
            margin: 12pt 0;
            font-size: 12pt;
        }

        .content th,
        .content td {
            border: 1pt solid #000000;
            padding: 4pt 6pt;
            text-align: left;
            vertical-align: top;
        }

        .content th {
            font-weight: bold;
        }

        /* Images */
        .content img {
            max-width: 100%;
            height: auto;
            margin: 12pt 0;
        }

        /* Horizontal Rule */
        .content hr {
            border: none;
            border-top: 1pt solid #000000;
            margin: 12pt 0;
        }

        /* Links */
        .content a {
            color: #000000;
            text-decoration: underline;
        }

        /* Page Break Control */
        .content h1, .content h2, .content h3 {
            page-break-after: avoid;
        }

        /* Print Styles */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Screen preview */
        @media screen {
            body {
                max-width: 21cm;
                margin: 20px auto;
                padding: 2.54cm;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>
<body>
    <!-- Header Dokumen -->
    <div class="document-header">
        <div class="document-title">{{ $note->title }}</div>
    </div>

    <!-- Konten Utama -->
    <div class="content">
        {!! format_note_content($note->content) !!}
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>
