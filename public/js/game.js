// ============================================
// FILE: public/js/game.js
// ============================================

/**
 * Game Edukasi Pancasila - Utilitas dan Helpers
 * File ini berisi fungsi-fungsi tambahan untuk game
 */

// ============================================
// 1. LOADING MANAGER
// ============================================

class LoadingManager {
    constructor() {
        this.totalAssets = 0;
        this.loadedAssets = 0;
        this.loadingScreen = null;
    }

    init() {
        this.loadingScreen = document.getElementById('loadingScreen');
        this.progressText = document.getElementById('loadingProgress');
    }

    incrementTotal() {
        this.totalAssets++;
    }

    incrementLoaded() {
        this.loadedAssets++;
        this.updateProgress();
    }

    updateProgress() {
        if (this.progressText) {
            const percentage = Math.round((this.loadedAssets / this.totalAssets) * 100);
            this.progressText.textContent = `Loading: ${percentage}%`;
        }

        if (this.loadedAssets === this.totalAssets) {
            this.hideLoading();
        }
    }

    hideLoading() {
        setTimeout(() => {
            if (this.loadingScreen) {
                this.loadingScreen.classList.add('hide');
                setTimeout(() => {
                    this.loadingScreen.style.display = 'none';
                }, 500);
            }
        }, 1000);
    }
}

// Instance global
const loadingManager = new LoadingManager();

// ============================================
// 2. SOUND MANAGER (Opsional)
// ============================================

class SoundManager {
    constructor() {
        this.sounds = {};
        this.muted = false;
    }

    loadSound(name, url) {
        const audio = new Audio(url);
        this.sounds[name] = audio;
    }

    play(name, loop = false) {
        if (this.muted || !this.sounds[name]) return;
        
        const sound = this.sounds[name];
        sound.loop = loop;
        sound.currentTime = 0;
        sound.play().catch(e => console.log('Audio play prevented:', e));
    }

    stop(name) {
        if (!this.sounds[name]) return;
        this.sounds[name].pause();
        this.sounds[name].currentTime = 0;
    }

    toggleMute() {
        this.muted = !this.muted;
        Object.values(this.sounds).forEach(sound => {
            sound.muted = this.muted;
        });
    }
}

// Instance global
const soundManager = new SoundManager();

// ============================================
// 3. PARTICLE SYSTEM (Efek Visual)
// ============================================

class ParticleSystem {
    constructor(scene) {
        this.scene = scene;
        this.particles = [];
    }

    createConfetti(position, color) {
        const geometry = new THREE.BoxGeometry(0.1, 0.1, 0.1);
        const material = new THREE.MeshBasicMaterial({ color: color });
        const particle = new THREE.Mesh(geometry, material);
        
        particle.position.copy(position);
        particle.velocity = new THREE.Vector3(
            (Math.random() - 0.5) * 0.2,
            Math.random() * 0.3 + 0.2,
            (Math.random() - 0.5) * 0.2
        );
        
        this.scene.add(particle);
        this.particles.push(particle);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            this.scene.remove(particle);
            this.particles = this.particles.filter(p => p !== particle);
        }, 3000);
    }

    createSuccessEffect(position) {
        const colors = [0xFFD700, 0xFFA500, 0xFF6B6B, 0x4ECDC4, 0xAA96DA];
        for (let i = 0; i < 20; i++) {
            setTimeout(() => {
                const color = colors[Math.floor(Math.random() * colors.length)];
                this.createConfetti(position, color);
            }, i * 50);
        }
    }

    update() {
        this.particles.forEach(particle => {
            particle.position.add(particle.velocity);
            particle.velocity.y -= 0.01; // Gravity
            particle.rotation.x += 0.1;
            particle.rotation.y += 0.1;
        });
    }
}

// ============================================
// 4. UTILITY FUNCTIONS
// ============================================

// Format angka dengan pemisah ribuan
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Hitung jarak 3D
function hitungJarak3D(pos1, pos2) {
    const dx = pos1.x - pos2.x;
    const dy = pos1.y - pos2.y;
    const dz = pos1.z - pos2.z;
    return Math.sqrt(dx * dx + dy * dy + dz * dz);
}

// Interpolasi linear
function lerp(start, end, t) {
    return start + (end - start) * t;
}

// Random range
function randomRange(min, max) {
    return Math.random() * (max - min) + min;
}

// Delay promise
function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// ============================================
// 5. LOCAL STORAGE MANAGER
// ============================================

class GameStorage {
    constructor() {
        this.prefix = 'game_pancasila_';
    }

    save(key, value) {
        try {
            localStorage.setItem(this.prefix + key, JSON.stringify(value));
            return true;
        } catch (e) {
            console.error('Error saving to localStorage:', e);
            return false;
        }
    }

    load(key, defaultValue = null) {
        try {
            const value = localStorage.getItem(this.prefix + key);
            return value ? JSON.parse(value) : defaultValue;
        } catch (e) {
            console.error('Error loading from localStorage:', e);
            return defaultValue;
        }
    }

    remove(key) {
        localStorage.removeItem(this.prefix + key);
    }

    clear() {
        const keys = Object.keys(localStorage);
        keys.forEach(key => {
            if (key.startsWith(this.prefix)) {
                localStorage.removeItem(key);
            }
        });
    }

    // Simpan high score
    saveHighScore(score) {
        const highScore = this.load('high_score', 0);
        if (score > highScore) {
            this.save('high_score', score);
            return true;
        }
        return false;
    }

    getHighScore() {
        return this.load('high_score', 0);
    }
}

// Instance global
const gameStorage = new GameStorage();

// ============================================
// 6. PERFORMANCE MONITOR
// ============================================

class PerformanceMonitor {
    constructor() {
        this.fps = 0;
        this.frameCount = 0;
        this.lastTime = performance.now();
    }

    update() {
        this.frameCount++;
        const currentTime = performance.now();
        const delta = currentTime - this.lastTime;
        
        if (delta >= 1000) {
            this.fps = Math.round((this.frameCount * 1000) / delta);
            this.frameCount = 0;
            this.lastTime = currentTime;
        }
    }

    getFPS() {
        return this.fps;
    }
}

// ============================================
// 7. KEYBOARD HANDLER
// ============================================

class KeyboardHandler {
    constructor() {
        this.keys = {};
        this.init();
    }

    init() {
        window.addEventListener('keydown', (e) => {
            this.keys[e.key.toLowerCase()] = true;
        });

        window.addEventListener('keyup', (e) => {
            this.keys[e.key.toLowerCase()] = false;
        });
    }

    isPressed(key) {
        return this.keys[key.toLowerCase()] || false;
    }

    reset() {
        this.keys = {};
    }
}

// ============================================
// 8. MOBILE TOUCH CONTROLS (Bonus)
// ============================================

class TouchControls {
    constructor(container) {
        this.container = container;
        this.touchStart = { x: 0, y: 0 };
        this.touchCurrent = { x: 0, y: 0 };
        this.isActive = false;
        this.init();
    }

    init() {
        this.container.addEventListener('touchstart', (e) => {
            this.isActive = true;
            const touch = e.touches[0];
            this.touchStart.x = touch.clientX;
            this.touchStart.y = touch.clientY;
        });

        this.container.addEventListener('touchmove', (e) => {
            if (!this.isActive) return;
            const touch = e.touches[0];
            this.touchCurrent.x = touch.clientX;
            this.touchCurrent.y = touch.clientY;
        });

        this.container.addEventListener('touchend', () => {
            this.isActive = false;
            this.touchStart = { x: 0, y: 0 };
            this.touchCurrent = { x: 0, y: 0 };
        });
    }

    getDirection() {
        if (!this.isActive) return { x: 0, y: 0 };
        
        const dx = this.touchCurrent.x - this.touchStart.x;
        const dy = this.touchCurrent.y - this.touchStart.y;
        
        return {
            x: Math.abs(dx) > 30 ? Math.sign(dx) : 0,
            y: Math.abs(dy) > 30 ? Math.sign(dy) : 0
        };
    }
}

// ============================================
// 9. ANALYTICS TRACKER (Opsional)
// ============================================

class GameAnalytics {
    constructor() {
        this.events = [];
    }

    trackEvent(category, action, label, value) {
        const event = {
            timestamp: new Date().toISOString(),
            category,
            action,
            label,
            value
        };
        
        this.events.push(event);
        console.log('Analytics:', event);
        
        // Bisa dikirim ke server untuk analisis
    }

    trackNPCInteraction(npcName) {
        this.trackEvent('NPC', 'interaction', npcName);
    }

    trackQuestionAnswer(npcName, correct) {
        this.trackEvent('Question', correct ? 'correct' : 'wrong', npcName);
    }

    trackGameComplete(score, time) {
        this.trackEvent('Game', 'complete', 'score', score);
        this.trackEvent('Game', 'complete', 'time', time);
    }
}

// Instance global
const gameAnalytics = new GameAnalytics();

// ============================================
// 10. HELPER FUNCTIONS UNTUK DEBUG
// ============================================

function showDebugInfo(info) {
    if (window.location.hostname === 'localhost') {
        console.log('[DEBUG]', info);
    }
}

function createDebugUI() {
    const debugDiv = document.createElement('div');
    debugDiv.id = 'debug-ui';
    debugDiv.style.cssText = `
        position: fixed;
        top: 100px;
        right: 10px;
        background: rgba(0,0,0,0.8);
        color: #0f0;
        padding: 10px;
        font-family: monospace;
        font-size: 12px;
        z-index: 10000;
        border-radius: 5px;
        display: none;
    `;
    document.body.appendChild(debugDiv);
    return debugDiv;
}

// Toggle debug mode dengan tombol ~
let debugMode = false;
window.addEventListener('keydown', (e) => {
    if (e.key === '`' || e.key === '~') {
        debugMode = !debugMode;
        const debugUI = document.getElementById('debug-ui');
        if (debugUI) {
            debugUI.style.display = debugMode ? 'block' : 'none';
        }
    }
});

// Export untuk digunakan di file lain
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        loadingManager,
        soundManager,
        ParticleSystem,
        gameStorage,
        gameAnalytics
    };
}