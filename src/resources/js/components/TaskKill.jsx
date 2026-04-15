import React, { useEffect, useRef, useState } from "react";

export default function TaskKill({ tasks = [], executeUrl }) {

    const [index, setIndex] = useState(0);
    const [isCutting, setIsCutting] = useState(false);

    const indexRef = useRef(0);
    const holdingRef = useRef(false);
    const runningRef = useRef(false);
    const mountedRef = useRef(true);

    // ★ SE
    const slashAudioRef = useRef(null);

    const currentTask = tasks[index] ?? null;

    // =========================
    // API
    // =========================
    const executeKill = async (task) => {

        if (!task) return false;

        try {

            const response = await fetch(executeUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content"),
                },
                body: JSON.stringify({
                    task_id: task.id,
                }),
            });

            if (!response.ok) {
                console.error("HTTP ERROR:", response.status);
                return false;
            }

            const data = await response.json();

            if (!data.success) {
                return false;
            }

            return true;

        } catch (e) {
            console.error("Fetch error:", e);
            return false;
        }
    };

    // =========================
    // SE 初期化
    // =========================
    useEffect(() => {

        slashAudioRef.current = new Audio("/sounds/slash.mp3");

        // 音量調整
        slashAudioRef.current.volume = 0.6;

    }, []);

    // =========================
    // SE 再生
    // =========================
    const playSlash = () => {

        const audio = slashAudioRef.current;

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

    // =========================
    // 1タスク討伐
    // =========================
    const killOnce = async () => {

        if (runningRef.current) return;

        const task = tasks[indexRef.current];

        if (!task) return;

        runningRef.current = true;

        // 斬撃開始
        setIsCutting(true);

        // ★ SE再生
        playSlash();

        // debug
        console.log("cut start");

        const success = await executeKill(task);

        if (success) {

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

        <div style={{ textAlign: "center" }}>

            <div className="tk-kill-card">

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

            </div>

            {/* 討伐ボタン */}
            <button
                className="tk-kill-btn"
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
                討伐
            </button>

        </div>
    );
}