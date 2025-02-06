function showLog(filename) {
    document.querySelectorAll('.log-content').forEach(el => el.style.display = 'none');

    const logDiv = document.getElementById('log-' + filename);
    if (logDiv) {
        logDiv.style.display = 'block';
    }
}

function updateURL() {
    const lines = document.getElementById('lines').value;

    if (!lines || lines <= 0) {
        alert('Please enter a valid number of lines.');
        return;
    }

    const url = new URL(window.location.href);
    url.searchParams.set('lines', lines);
    window.location.href = url.toString();
}

function loadLogContent(filename, inputId) {
    const linesInput = document.getElementById(inputId);
    const lines = linesInput ? linesInput.value : 100;

    if (!lines || lines <= 0) {
        alert('Please enter a valid number of lines.');
        return;
    }

    const reloadUrl = `/cms/api/logViewer/${encodeURIComponent(filename)}/${encodeURIComponent(lines)}`;
    fetch(reloadUrl, {
        method: "GET",
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP-Error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                const logTitle = document.getElementById('logTitle');
                const logLines = document.getElementById('logLines');
                const logContent = document.getElementById('logContent');

                logTitle.textContent = `Viewing Log: ${data.filename}`;
                logLines.textContent = data.lines;

                logContent.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error fetching log content:', error);
            alert('Failed to load the log content.');
        });
}

let liveInterval = null;
let activeLog = null;

function toggleLiveMode(filename, inputId, button) {
    const linesInput = document.getElementById(inputId);

    if (!linesInput || !linesInput.value || linesInput.value <= 0) {
        alert('Please enter a valid number of lines.');
        return;
    }

    if (button.textContent.trim() === 'Live') {

        if (liveInterval) {
            alert('Another log is already in live mode. Please stop it first.');
            return;
        }

        activeLog = { filename };
        button.textContent = 'Stop';
        button.classList.remove('btn-success');
        button.classList.add('btn-danger');

        liveInterval = setInterval(() => {
            const lines = linesInput.value;
            if (!lines || lines <= 0) {
                alert('Please enter a valid number of lines.');
                stopLiveMode(button);
                return;
            }

            fetch(`/cms/api/logViewer/${encodeURIComponent(filename)}/${encodeURIComponent(lines)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP-Error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        stopLiveMode(button);
                    } else {
                        const logTitle = document.getElementById('logTitle');
                        const logLines = document.getElementById('logLines');
                        const logContent = document.getElementById('logContent');

                        logTitle.textContent = `Viewing Log (Live): ${data.filename}`;
                        logLines.textContent = data.lines;

                        logContent.style.display = 'block';
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

        alert('Live mode stopped.');
    }

    activeLog = null;

    button.textContent = 'Live';
    button.classList.remove('btn-danger');
    button.classList.add('btn-success');
}