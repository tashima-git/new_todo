import React, { useEffect, useRef, useState } from "react";

export default function TaskKill({ tasks = [], executeUrl }) {

    const [index, setIndex] = useState(0);
    const [isCutting, setIsCutting] = useState(false);

    const indexRef = useRef(0);
    const holdingRef = useRef(false);
    const runningRef = useRef(false);
    const mountedRef = useRef(true);

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
    // 1タスク討伐
    // =========================
    const killOnce = async () => {

        if (runningRef.current) return;

        const task = tasks[indexRef.current];

        if (!task) return;

        runningRef.current = true;

        setIsCutting(true);

        const success = await executeKill(task);

        if (success) {

            await new Promise(r => setTimeout(r, 250));

            const nextIndex = indexRef.current + 1;

            indexRef.current = nextIndex;
            setIndex(nextIndex);

            if (nextIndex >= tasks.length) {

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

            await new Promise(r => setTimeout(r, 30));
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

            <div
                className={`tk-kill-card ${isCutting ? "tk-cut" : ""}`}
                style={{
                    position: "relative",
                    padding: "40px",
                    border: "1px solid #ddd",
                    borderRadius: "8px",
                    background: "#fff",
                    userSelect: "none",
                }}
            >

                <div style={{ fontSize: "14px", opacity: 0.6 }}>
                    #{currentTask.id}
                </div>

                <div style={{ fontSize: "20px", marginTop: "10px" }}>
                    {currentTask.title}
                </div>

                {isCutting && (
                    <div
                        style={{
                            position: "absolute",
                            top: "50%",
                            left: "-20%",
                            width: "140%",
                            height: "2px",
                            background: "white",
                            boxShadow: "0 0 12px white",
                            transform: "rotate(-20deg)",
                            animation: "slash 0.3s linear forwards",
                        }}
                    />
                )}

            </div>

            <button
                onMouseDown={startRapid}
                onMouseUp={stopRapid}
                onMouseLeave={stopRapid}
                onClick={() => {
                    if (!holdingRef.current) killOnce();
                }}
                style={{
                    marginTop: "24px",
                    padding: "10px 24px",
                    fontSize: "16px",
                    cursor: "pointer",
                }}
            >
                討伐する
            </button>

            <style>{`
                @keyframes slash {
                    0% {
                        transform: translateX(-100%) rotate(-20deg);
                        opacity:1;
                    }
                    100% {
                        transform: translateX(100%) rotate(-20deg);
                        opacity:0;
                    }
                }

                .tk-cut {
                    animation: shake 0.3s ease;
                }

                @keyframes shake {
                    0% { transform: translateX(0); }
                    25% { transform: translateX(-4px); }
                    50% { transform: translateX(4px); }
                    75% { transform: translateX(-2px); }
                    100% { transform: translateX(0); }
                }
            `}</style>

        </div>
    );
}