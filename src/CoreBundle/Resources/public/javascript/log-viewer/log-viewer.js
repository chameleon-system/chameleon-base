let liveInterval = null;
let activeLog = null;

document.addEventListener('DOMContentLoaded', () => {
    const logFilterInput = document.getElementById('logFilter');
    if (logFilterInput) {
        logFilterInput.addEventListener('input', filterLogs);
    }
});

function filterLogs() {
    const filterText = document.getElementById('logFilter').value.toLowerCase();
    const logLines = document.getElementById('logLines');

    if (!logLines) return;

    const lines = logLines.textContent.split('\n');
    const filteredLines = lines.filter(line => line.toLowerCase().includes(filterText));

    logLines.textContent = filteredLines.join('\n');
}

function showLogContainer(title) {
    const logContent = document.getElementById('logContent');
    const logTitle = document.getElementById('logTitle');
    const logLines = document.getElementById('logLines');
    const logLoading = document.getElementById('logLoading');

    logTitle.textContent = title;
    logLines.textContent = ''; // Reset previous log content
    logLoading.style.display = 'block';
    logContent.style.display = 'block';
}

function hideLoader() {
    const logLoading = document.getElementById('logLoading');
    if (logLoading) {
        logLoading.style.display = 'none';
    }
}

function loadLogContent(filename, inputId) {
    const linesInput = document.getElementById(inputId);
    const lines = linesInput ? linesInput.value : 100;

    if (!lines || lines <= 0) {
        toasterMessage('Please enter a valid number of lines', 'ERROR');
        return;
    }

    showLogContainer(`Log: ${filename}`);

    fetch(`/cms/api/logViewer/${encodeURIComponent(filename)}/${encodeURIComponent(lines)}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                const logLines = document.getElementById('logLines');
                logLines.textContent = data.lines;
                hideLoader();
                filterLogs(); // Apply filter after loading
            }
        })
        .catch(error => {
            console.error('Error fetching log content:', error);
            toasterMessage('Failed to load the log content', 'ERROR');
            hideLoader();
        });
}

function toggleLiveMode(filename, inputId, button) {
    const linesInput = document.getElementById(inputId);

    if (!linesInput || !linesInput.value || linesInput.value <= 0) {
        toasterMessage('Please enter a valid number of lines', 'ERROR');
        return;
    }

    if (button.textContent.trim() === 'Live') {
        if (liveInterval) {
            toasterMessage('Another log is already in live mode. Please stop it first.', 'WARNING');
            return;
        }

        activeLog = { filename };
        button.textContent = 'Stop';
        button.classList.remove('btn-success');
        button.classList.add('btn-danger');

        showLogContainer(`Viewing Log (Live): ${filename}`);

        liveInterval = setInterval(() => {
            fetch(`/cms/api/logViewer/${encodeURIComponent(filename)}/${encodeURIComponent(linesInput.value)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        stopLiveMode(button);
                    } else {
                        document.getElementById('logLines').textContent = data.lines;
                        hideLoader();
                        filterLogs(); // Live filtering
                    }
                })
                .catch(error => {
                    console.error('Error fetching log content in live mode:', error);
                    stopLiveMode(button);
                });
        }, 2000);
    } else {
        stopLiveMode(button);
    }
}

function stopLiveMode(button) {
    if (liveInterval) {
        clearInterval(liveInterval);
        liveInterval = null;
        toasterMessage(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.log_viewer.live_view_stopped'), 'SUCCESS');
    }

    activeLog = null;

    if (button) {
        button.textContent = 'Live';
        button.classList.remove('btn-danger');
        button.classList.add('btn-success');
    }
}
