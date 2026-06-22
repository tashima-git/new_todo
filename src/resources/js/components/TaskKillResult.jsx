import React, { useEffect, useMemo, useRef, useState } from 'react';

const statLabels = {
    patience: '忍耐',
    speed: '迅速',
    focus: '集中',
    accuracy: '正確',
    life: '生活力',
    strategy: '戦略',
};

const bossTypeLabels = {
    mob: '雑魚',
    mid: '中ボス',
    boss: '大ボス',
};

export default function TaskKillResult({
    logs = [],
    total = {},
    tasksUrl = '/tasks',
    recordUrl = '/record',
}) {

    // =============================
    // 安全なデータ生成
    // =============================
    const safeLogs = Array.isArray(logs?.data)
        ? logs.data
        : Array.isArray(logs)
        ? logs
        : [];

    const safeTotal = total ?? {};
    const defeatedCount = safeLogs.length;

    const formattedLogs = useMemo(() => {
        return safeLogs.map((log, index) => {
            const dateSource = log?.task_completed_at ?? log?.created_at ?? null;
            const date = dateSource ? new Date(dateSource) : null;

            return {
                id: log?.id ?? `log-${index}`,
                title: log?.task_title ?? '-',
                bossType: bossTypeLabels[log?.boss_type] ?? '不明',
                defeatedAt: date && !Number.isNaN(date.getTime())
                    ? date.toLocaleString('ja-JP', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                    })
                    : '-',
            };
        });
    }, [safeLogs]);

    // =============================
    // state
    // =============================
    const [showLogs, setShowLogs] = useState(false);

    const [displayStats, setDisplayStats] = useState({
        patience: 0,
        speed: 0,
        focus: 0,
        accuracy: 0,
        life: 0,
        strategy: 0,
    });

    const countIntervalsRef = useRef([]);
    const fadeTimerRef = useRef(null);

    // =============================
    // フェードイン開始
    // =============================
    useEffect(() => {

        fadeTimerRef.current = setTimeout(() => {

            setShowLogs(true);

            // フェードイン後ステータスカウント開始
            setTimeout(startCountUp, 400);

        }, 600);

        return () => {
            if (fadeTimerRef.current) clearTimeout(fadeTimerRef.current);
        };

    }, [safeLogs]);

    // =============================
    // ステータスカウントアップ
    // =============================
    const startCountUp = () => {

        Object.keys(statLabels).forEach(key => {

            const target = Number(safeTotal[key] ?? 0);

            if (!target) return;

            let current = 0;

            const step = Math.max(1, Math.ceil(target / 20));

            const interval = setInterval(() => {

                current += step;

                if (current >= target) {
                    current = target;
                    clearInterval(interval);
                }

                setDisplayStats(prev => ({
                    ...prev,
                    [key]: current
                }));

            }, 40);

            countIntervalsRef.current.push(interval);

        });

    };

    // =============================
    // cleanup
    // =============================
    useEffect(() => {
        return () => {

            if (fadeTimerRef.current) {
                clearTimeout(fadeTimerRef.current);
            }

            countIntervalsRef.current.forEach(clearInterval);

        };
    }, []);

    // =============================
    // UI
    // =============================
    return (

        <div className="tk-result-screen">
            <header className="tk-result-title-frame">
                <span className="tk-result-title-mark">◇</span>
                <h1>討伐結果</h1>
                <span className="tk-result-title-mark">◇</span>
            </header>

            <section className="tk-result-panel">
                <div className="tk-result-complete">
                    <div className="tk-result-swords" aria-hidden="true" />
                    <div className="tk-result-complete-text">討伐完了</div>
                    <div className="tk-result-slash-line" aria-hidden="true" />
                </div>

                <div className="tk-result-count-box">
                    <span>討伐数</span>
                    <strong>{defeatedCount}</strong>
                    <span>体</span>
                </div>

                <div className="tk-result-section-label">
                    <span>獲得ステータス</span>
                </div>

                <div className="tk-result-stats-grid">
                    {Object.entries(statLabels).map(([key, label]) => (
                        <div className="tk-result-stat-card" key={key}>
                            <span className="tk-result-stat-label">{label}</span>
                            <strong className="tk-result-stat-value">
                                +{displayStats[key]}
                            </strong>
                        </div>
                    ))}
                </div>

                <div className="tk-result-log-heading">
                    <span>今回の討伐</span>
                </div>

                <div className={`tk-result-log-table ${showLogs ? 'is-visible' : ''}`}>
                    <div className="tk-result-log-row tk-result-log-row--head">
                        <span>状態</span>
                        <span>タスク名</span>
                        <span>ボス種別</span>
                        <span>討伐日時</span>
                    </div>

                    <div className={formattedLogs.length > 10 ? 'tk-result-log-body is-scrollable' : 'tk-result-log-body'}>
                        {formattedLogs.length === 0 && (
                            <div className="tk-result-empty-log">
                                討伐ログがありません。
                            </div>
                        )}

                        {formattedLogs.map(log => (
                            <div className="tk-result-log-row" key={log.id}>
                                <span>
                                    <span className="tk-result-status-badge">討伐済</span>
                                </span>
                                <span className="tk-result-task-title">{log.title}</span>
                                <span>
                                    <span className="tk-result-type-badge">{log.bossType}</span>
                                </span>
                                <span className="tk-result-date">{log.defeatedAt}</span>
                            </div>
                        ))}
                    </div>
                </div>

                <div className="tk-result-actions">
                    <a className="tk-result-button" href={tasksUrl}>
                        ▶ タスク一覧へ戻る
                    </a>
                    <a className="tk-result-button" href={recordUrl}>
                        ▶ 戦績を見る
                    </a>
                </div>
            </section>
        </div>
    );
}