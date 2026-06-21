import React, { useEffect, useRef, useState } from "react";

export default function TaskKill({ tasks = [], executeUrl, taskkillSeVolume = 50 }) {

    const [index, setIndex] = useState(0);
    const [isCutting, setIsCutting] = useState(false);
    const [isBulkCutting, setIsBulkCutting] = useState(false);
    const [isExecuting, setIsExecuting] = useState(false);
    const [errorMessage, setErrorMessage] = useState("");

    const indexRef = useRef(0);
    const holdingRef = useRef(false);
    const runningRef = useRef(false);
    const mountedRef = useRef(true);
    const executedRef = useRef(false);

    // ★ SE
    const slashAudioRef = useRef(null);
    const layeredSlashAudioRef = useRef(null);

    const currentTask = tasks[index] ?? null;

    // =========================
    // API
    // =========================
    const executeAllKills = async () => {

        if (executedRef.current) return true;
        if (tasks.length === 0) return false;

        try {
            setIsExecuting(true);
            setErrorMessage("");

            const response = await fetch(executeUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content"),
                },
                body: JSON.stringify({
                    task_ids: tasks.map(task => task.id),
                }),
            });

            if (!response.ok) {
                console.error("HTTP ERROR:", response.status);
                setErrorMessage("討伐処理に失敗しました。画面を更新してもう一度試してください。");
                return false;
            }

            const data = await response.json();

            if (!data.success) {
                setErrorMessage("討伐処理に失敗しました。画面を更新してもう一度試してください。");
                return false;
            }

            executedRef.current = true;
            return true;

        } catch (e) {
            console.error("Fetch error:", e);
            setErrorMessage("通信に失敗しました。ネットワーク状態を確認してください。");
            return false;
        } finally {
            setIsExecuting(false);
        }
    };

    // =========================
    // SE 初期化
    // =========================
    useEffect(() => {

        slashAudioRef.current = new Audio("/sounds/slash.mp3");
        layeredSlashAudioRef.current = new Audio("/sounds/slash.mp3");

        const volume = Number.isFinite(taskkillSeVolume) ? taskkillSeVolume : 50;
        const normalizedVolume = Math.min(Math.max(volume, 0), 100) / 100;
        slashAudioRef.current.volume = normalizedVolume;
        layeredSlashAudioRef.current.volume = normalizedVolume;

    }, [taskkillSeVolume]);

    // =========================
    // SE 再生
    // =========================
    const playSlash = (audio = slashAudioRef.current) => {

        if (!audio) return;

        audio.currentTime = 0;

        // クリティカル判定
        const isHeavy = Math.random() < 0.1;

        if (isHeavy) {

            audio.playbackRate = 0.9;

        } else {

            audio.playbackRate = 1.25 + Math.random() * 0.25;

        }

        audio.play().catch(() => {});
    };

    const playLayeredSlash = () => {
        playSlash(slashAudioRef.current);

        window.setTimeout(() => {
            playSlash(layeredSlashAudioRef.current);
        }, 90);
    };

    // =========================
    // 1タスク討伐
    // =========================
    const killOnce = async () => {

        if (runningRef.current) return;

        const task = tasks[indexRef.current];

        if (!task) return;

        runningRef.current = true;

        const executed = await executeAllKills();

        if (!executed) {
            runningRef.current = false;
            return;
        }

        // 斬撃開始
        setIsBulkCutting(false);
        setIsCutting(true);

        // ★ SE再生
        playSlash();

        // debug
        console.log("cut start");

        // エフェクトを見せる時間
        await new Promise(r => setTimeout(r, 250));

        const nextIndex = indexRef.current + 1;

        indexRef.current = nextIndex;
        setIndex(nextIndex);

        if (nextIndex >= tasks.length) {

            await new Promise(r => setTimeout(r, 200));

            window.location.href = "/taskkill/result";
            return;
        }

        setIsCutting(false);

        runningRef.current = false;
    };

    // =========================
    // 長押しループ
    // =========================
    const autoKillLoop = async () => {

        while (holdingRef.current && mountedRef.current) {

            await killOnce();

            // 次の斬撃までの間隔
            await new Promise(r => setTimeout(r, 50));
        }
    };

    // =========================
    // 長押し開始
    // =========================
    const startRapid = () => {

        if (holdingRef.current) return;

        holdingRef.current = true;

        autoKillLoop();
    };

    // =========================
    // 長押し終了
    // =========================
    const stopRapid = () => {

        holdingRef.current = false;
    };

    const waitForNextFrame = () => new Promise(resolve => {
        requestAnimationFrame(() => {
            requestAnimationFrame(resolve);
        });
    });

    const skipToResult = async () => {

        if (runningRef.current) return;

        runningRef.current = true;
        holdingRef.current = false;

        const executed = await executeAllKills();

        if (executed) {
            setIsCutting(false);
            setIsBulkCutting(false);
            await waitForNextFrame();

            setIsBulkCutting(true);
            setIsCutting(true);
            await waitForNextFrame();

            playLayeredSlash();

            await new Promise(r => setTimeout(r, 720));

            window.location.href = "/taskkill/result";
            return;
        }

        runningRef.current = false;
    };

    // =========================
    // cleanup
    // =========================
    useEffect(() => {

        return () => {

            mountedRef.current = false;
            holdingRef.current = false;

            // ★ Audio解放
            if (slashAudioRef.current) {

                slashAudioRef.current.pause();
                slashAudioRef.current = null;
            }

            if (layeredSlashAudioRef.current) {
                layeredSlashAudioRef.current.pause();
                layeredSlashAudioRef.current = null;
            }
        };

    }, []);

    // =========================
    // タスク無し
    // =========================
    if (!currentTask) {

        return (
            <div style={{ textAlign: "center", padding: "40px" }}>
                討伐対象なし
            </div>
        );
    }

    return (

        <div className="tk-kill-screen">

            <div className={`tk-kill-card ${isBulkCutting ? "bulk-cut" : ""}`}>

                {/* 上パーツ */}
                <div className={`tk-card-layer top ${isCutting ? "cut" : ""}`}>

                    <div className="tk-kill-id">
                        #{currentTask.id}
                    </div>

                    <div className="tk-kill-title">
                        {currentTask.title}
                    </div>

                </div>

                {/* 下パーツ */}
                <div className={`tk-card-layer bottom ${isCutting ? "cut" : ""}`}>

                    <div className="tk-kill-id">
                        #{currentTask.id}
                    </div>

                    <div className="tk-kill-title">
                        {currentTask.title}
                    </div>

                </div>

                {/* 斬撃エフェクト */}
                {isCutting && <div className="tk-slash" />}
                {isCutting && isBulkCutting && <div className="tk-slash tk-slash--mirror" />}

            </div>

            {/* 討伐ボタン */}
            <button
                className="tk-kill-btn"
                disabled={isExecuting}
                onMouseDown={startRapid}
                onMouseUp={stopRapid}
                onMouseLeave={stopRapid}
                onClick={() => {

                    // クリック単発用
                    if (!holdingRef.current) {
                        killOnce();
                    }
                }}
            >
                {isExecuting ? "討伐準備中..." : "討伐"}
            </button>

            <div className="tk-kill-bulk-action">
                <button
                    type="button"
                    className="tk-kill-bulk-btn"
                    disabled={isExecuting}
                    onClick={skipToResult}
                >
                    {isExecuting ? "討伐準備中..." : "まとめて討伐"}
                </button>
            </div>

            {errorMessage && (
                <div style={{ color: "#b91c1c", marginTop: "16px" }}>
                    {errorMessage}
                </div>
            )}

        </div>
    );
}