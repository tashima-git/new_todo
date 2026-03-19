import React, { useState, useRef } from 'react';

export default function TaskKillBattle({ taskIds, executeUrl }) {
    const [loading, setLoading] = useState(false);
    const sendingRef = useRef(false);

    const handleKill = async () => {
        if (sendingRef.current) return;
        sendingRef.current = true;

        if (!taskIds || taskIds.length === 0) {
            alert("討伐対象がありません");
            sendingRef.current = false;
            return;
        }

        setLoading(true);

        try {
            const token = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content');

            const response = await fetch(executeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    task_ids: taskIds
                })
            });

            if (!response.ok) {
                throw new Error("server error");
            }

            // 必要ならレスポンス取得
            // const result = await response.json();

            location.reload();

        } catch (err) {
            console.error(err);
            alert("討伐処理でエラーが発生しました");
        }

        setLoading(false);
        sendingRef.current = false;
    };

    return (
        <div style={{ textAlign: "right" }}>
            <button
                type="button"
                onClick={handleKill}
                disabled={loading}
                style={{
                    padding: "10px 20px",
                    fontSize: "16px",
                    background: "#c53030",
                    color: "white",
                    border: "none",
                    borderRadius: "6px"
                }}
            >
                {loading ? "討伐中..." : `討伐開始（${taskIds.length}件）`}
            </button>
        </div>
    );
}