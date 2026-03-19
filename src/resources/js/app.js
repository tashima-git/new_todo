// ========================================
// app.js（完全版）
// ========================================

// Laravel の bootstrap
require('./bootstrap');

import React from 'react';
import ReactDOM from 'react-dom/client';

// React コンポーネント
import TaskKill from './components/TaskKill';
import TaskKillResult from './components/TaskKillResult';

// ========================================
// TaskKill（討伐タスク画面）マウント
// ========================================
const taskKillEl = document.getElementById('taskkill-root');

if (taskKillEl) {
    try {
        // Blade 側から渡されたタスクデータと実行 URL を取得
        const tasks = JSON.parse(taskKillEl.dataset.tasks || '[]');
        const executeUrl = taskKillEl.dataset.executeUrl || null;

        const root = ReactDOM.createRoot(taskKillEl);

        root.render(
                <TaskKill
                    tasks={tasks}
                    executeUrl={executeUrl}
                />
        );

    } catch (error) {
        console.error('TaskKill mount error:', error);
    }
}

// ========================================
// TaskKillResult（討伐結果画面）マウント
// ========================================
const resultEl = document.getElementById('taskkill-result-root');

if (resultEl) {
    try {
        // Blade 側から渡されたログと合計値データを取得
        const logs = JSON.parse(resultEl.dataset.logs || '[]');
        const total = JSON.parse(resultEl.dataset.total || '{}');

        const root = ReactDOM.createRoot(resultEl);

        root.render(
            <React.StrictMode>
                <TaskKillResult
                    logs={logs}
                    total={total}
                />
            </React.StrictMode>
        );

    } catch (error) {
        console.error('TaskKillResult mount error:', error);
    }
}