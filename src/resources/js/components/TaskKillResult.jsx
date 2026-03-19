import React, { useEffect, useRef, useState } from 'react';

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

export default function TaskKillResult({ logs = [], total = {} }) {

    // =============================
    // 安全なデータ生成
    // =============================
    const safeLogs = Array.isArray(logs?.data)
        ? logs.data
        : Array.isArray(logs)
        ? logs
        : [];

    const safeTotal = total ?? {};

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

        <div className="space-y-6">

            <div>
                <h1 className="text-2xl font-bold">
                    討伐結果
                </h1>

                <p className="text-sm text-gray-600 mt-1">
                    今日の討伐が完了しました。ステータスが上昇しています。
                </p>
            </div>

            {/* =============================
                獲得ステータス
            ============================= */}

            <div className="rounded border bg-white p-4">

                <div className="font-bold mb-3">
                    今回の獲得ステータス
                </div>

                <div className="grid grid-cols-2 gap-3 text-sm">

                    {Object.entries(statLabels).map(([key, label]) => (

                        <div
                            key={key}
                            className="flex items-center justify-between border rounded px-3 py-2"
                        >
                            <span>{label}</span>

                            <span className="font-bold">
                                +{displayStats[key]}
                            </span>

                        </div>

                    ))}

                </div>

            </div>

            {/* =============================
                討伐ログ
            ============================= */}

            <div className="rounded border bg-white p-4">

                <div className="flex items-center justify-between mb-3">

                    <div className="font-bold">
                        討伐した敵
                    </div>

                    <div className="text-sm text-gray-600">
                        {safeLogs.length}体
                    </div>

                </div>

                {safeLogs.length === 0 && (
                    <div className="text-sm text-gray-600">
                        討伐ログがありません。
                    </div>
                )}

                <div className="space-y-3">

                    {safeLogs.map((log, i) => {

                        if (!log) return null;

                        const date = log.task_completed_at
                            ? new Date(log.task_completed_at)
                            : null;

                        return (

                            <div
                                key={`taskkill-log-${log.id ?? i}`}
                                className={`
                                    rounded border p-3 bg-white
                                    transition-opacity duration-500
                                    ${showLogs ? "opacity-100" : "opacity-0"}
                                `}
                            >

                                <div className="font-bold">

                                    <span className="ml-2 text-sm text-gray-600">
                                        {bossTypeLabels[log.boss_type] ?? '不明'}
                                    </span>

                                </div>

                                <div className="text-sm mt-1">
                                    タスク名：{log.task_title ?? '-'}
                                </div>

                                <div className="text-xs text-gray-500 mt-1">

                                    討伐日時：

                                    {date
                                        ? date.toLocaleString('ja-JP')
                                        : '-'
                                    }

                                </div>

                            </div>

                        );

                    })}

                </div>

            </div>

        </div>
    );
}