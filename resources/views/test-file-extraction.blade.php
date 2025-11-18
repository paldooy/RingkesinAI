<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test File Extraction</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-4">🧪 Test Gemini API Integration</h1>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Document (PDF/DOCX/TXT)</label>
            <input type="file" id="documentFile" accept=".pdf,.doc,.docx,.txt" 
                   class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Custom Instructions (Optional)</label>
            <div id="instructionsContainer" class="space-y-2 mb-2">
                <!-- Instructions will be added here -->
            </div>
            <button onclick="addInstruction()" class="text-sm text-blue-600 hover:text-blue-800">
                + Add Instruction
            </button>
        </div>

        <button onclick="testSummarize()" 
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
            Generate Summary
        </button>

        <div id="loading" class="hidden mt-4 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-gray-600">Processing... Please wait</p>
        </div>

        <div id="result" class="mt-6 hidden">
            <h2 class="text-xl font-semibold mb-2">Result:</h2>
            <div id="resultContent" class="bg-gray-50 p-4 rounded-lg"></div>
        </div>

        <div id="error" class="mt-6 hidden">
            <h2 class="text-xl font-semibold text-red-600 mb-2">Error:</h2>
            <div id="errorContent" class="bg-red-50 p-4 rounded-lg text-red-800"></div>
        </div>
    </div>

    <script>
        let instructionCount = 0;

        function addInstruction() {
            instructionCount++;
            const container = document.getElementById('instructionsContainer');
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `
                <input type="text" class="instruction-input flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm" 
                       placeholder="e.g., Fokus pada poin-poin utama">
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800 px-3">×</button>
            `;
            container.appendChild(div);
        }

        async function testSummarize() {
            const fileInput = document.getElementById('documentFile');
            const file = fileInput.files[0];
            
            if (!file) {
                alert('Please select a file first!');
                return;
            }

            // Hide previous results
            document.getElementById('result').classList.add('hidden');
            document.getElementById('error').classList.add('hidden');
            document.getElementById('loading').classList.remove('hidden');

            // Collect instructions
            const instructionInputs = document.querySelectorAll('.instruction-input');
            const instructions = Array.from(instructionInputs)
                .map((input, index) => ({
                    id: index + 1,
                    text: input.value
                }))
                .filter(inst => inst.text.trim() !== '');

            // Prepare form data
            const formData = new FormData();
            formData.append('document', file);
            formData.append('instructions', JSON.stringify(instructions));

            try {
                const response = await fetch('/summarize', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();
                
                document.getElementById('loading').classList.add('hidden');

                if (data.success) {
                    const resultDiv = document.getElementById('resultContent');
                    resultDiv.innerHTML = `
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600">
                                <strong>File:</strong> ${data.fileName || 'N/A'}<br>
                                <strong>Model:</strong> ${data.model || 'N/A'}<br>
                                <strong>Tokens Used:</strong> ${data.tokens_used || 'N/A'}<br>
                                <strong>Cached:</strong> ${data.cached ? '✅ Yes' : '❌ No'}
                            </p>
                            <hr class="my-3">
                            <div class="prose max-w-none">
                                ${data.summary.replace(/\n/g, '<br>')}
                            </div>
                        </div>
                    `;
                    document.getElementById('result').classList.remove('hidden');
                } else {
                    document.getElementById('errorContent').innerHTML = data.error || 'Unknown error';
                    document.getElementById('error').classList.remove('hidden');
                }
            } catch (error) {
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('errorContent').innerHTML = error.message;
                document.getElementById('error').classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
