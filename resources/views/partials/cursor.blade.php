<!-- Custom Cursor Element -->
<div id="custom-cursor"></div>

<script>
// --- 1. AUDIO SYNTHESIZER (WEB AUDIO API) ---
const AudioContext = window.AudioContext || window.webkitAudioContext;
let audioCtx;

function playClickSound() {
    if (!audioCtx) {
        audioCtx = new AudioContext();
    }
    if (audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    
    const osc = audioCtx.createOscillator();
    const gainNode = audioCtx.createGain();
    
    osc.type = 'triangle';
    
    const now = audioCtx.currentTime;
    osc.frequency.setValueAtTime(120, now);
    osc.frequency.exponentialRampToValueAtTime(40, now + 0.08);
    
    gainNode.gain.setValueAtTime(1, now);
    gainNode.gain.exponentialRampToValueAtTime(0.01, now + 0.08);
    
    osc.connect(gainNode);
    gainNode.connect(audioCtx.destination);
    
    osc.start(now);
    osc.stop(now + 0.08);
}

// --- 2. CUSTOM CURSOR TRACKING ---
const cursor = document.getElementById('custom-cursor');

document.addEventListener('mousemove', (e) => {
    if (cursor) {
        cursor.style.left = e.clientX + 'px';
        cursor.style.top = e.clientY + 'px';
    }
});

// Deteksi elemen interaktif untuk efek Hover Kursor
const interactiveSelector = 'a, button, input[type="submit"], input[type="button"], select, .neo-card, .brutal-tab, .share-input-box, .neo-drop-zone, .brutal-hover';

document.addEventListener('mouseover', (e) => {
    if (e.target.closest(interactiveSelector)) {
        if (cursor) cursor.classList.add('hover');
    }
});

document.addEventListener('mouseout', (e) => {
    if (e.target.closest(interactiveSelector)) {
        if (cursor) cursor.classList.remove('hover');
    }
});

// Efek Klik & Suara pada Elemen Interaktif
document.addEventListener('mousedown', (e) => {
    if (cursor) cursor.classList.add('click');
    if (e.target.closest(interactiveSelector)) {
        playClickSound();
    }
});

document.addEventListener('mouseup', () => {
    if (cursor) cursor.classList.remove('click');
});
</script>
