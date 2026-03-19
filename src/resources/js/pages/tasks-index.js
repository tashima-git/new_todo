// ============================
// 初期状態：子タスクを閉じる
// ============================
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('tr[data-parent]').forEach(row => {
        row.style.display = 'none';
    });
});

// ============================
// アコーディオン（親子表示切替）
// ============================
document.addEventListener('click', e => {

    const btn = e.target.closest('.toggle-children');
    if (!btn) return;

    const parentId = btn.dataset.target;

    const children = document.querySelectorAll(
        `tr[data-parent="${parentId}"]`
    );

    if (children.length === 0) return;

    const isVisible =
        children[0].style.display !== 'none';

    if (isVisible) {

        children.forEach(child => closeRecursively(child));

        // ボタンを ▶ に戻す
        btn.textContent = '▶';

    } else {

        children.forEach(child => {
            child.style.display = '';
        });

        // ボタンを ▼ に変更
        btn.textContent = '▼';
    }

});

function closeRecursively(row) {

    const id = row.dataset.id;

    row.style.display = 'none';

    const grandchildren = document.querySelectorAll(
        `tr[data-parent="${id}"]`
    );

    grandchildren.forEach(child => closeRecursively(child));
}


// ============================
// CSRF helper
// ============================
function getCsrfToken() {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!token) {
    console.error('CSRF token not found. Did you forget <meta name="csrf-token"> in layout?');
  }
  return token;
}

async function postWithMethod(url, method) {
  const csrf = getCsrfToken();
  if (!csrf) {
    alert('CSRFトークンが見つかりません。layoutの<meta>を確認してください。');
    return;
  }

  const body = new URLSearchParams();
  body.append('_token', csrf);
  body.append('_method', method);

  const res = await fetch(url, {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
    body
  });

  if (res.ok) {
    location.reload();
    return;
  }

  let message = '操作に失敗しました。';
  const contentType = res.headers.get('content-type') || '';
  if (contentType.includes('application/json')) {
    const json = await res.json().catch(() => null);
    if (json?.message) message += '\n\n' + json.message;
  } else {
    const text = await res.text().catch(() => '');
    if (text) message += '\n\n' + text.slice(0, 500);
  }

  alert(message);
}

// ============================
// タスク操作ボタン
// ============================
document.addEventListener('click', async e => {
    const completeBtn = e.target.closest('.btn-complete');
    if (completeBtn) { await postWithMethod(completeBtn.dataset.url, 'PATCH'); return; }

    const uncompleteBtn = e.target.closest('.btn-uncomplete');
    if (uncompleteBtn) { await postWithMethod(uncompleteBtn.dataset.url, 'PATCH'); return; }

    const deleteBtn = e.target.closest('.btn-delete');
    if (deleteBtn) {
        if (!confirm('このタスクを削除しますか？')) return;
        await postWithMethod(deleteBtn.dataset.url, 'DELETE'); return;
    }
});

// ============================
// 一括操作（bulk）
// ============================
function updateBulkSubmitState() {
    const action = document.getElementById('bulkAction');
    const submit = document.getElementById('bulkSubmit');
    const checks = document.querySelectorAll('.task-check');

    if (!action || !submit || checks.length === 0) return;

    const anyChecked = Array.from(checks).some(ch => ch.checked);
    const hasAction = action.value !== '';

    submit.disabled = !(anyChecked && hasAction);
}

function setAllChecks(checked) {
    const checks = document.querySelectorAll('.task-check');
    checks.forEach(ch => ch.checked = checked);
}

const checkAll = document.getElementById('checkAll');
if (checkAll) {
    checkAll.addEventListener('change', () => {
        setAllChecks(checkAll.checked);
        updateBulkSubmitState();
    });
}

document.querySelectorAll('.task-check').forEach(ch => {
    ch.addEventListener('change', () => {
        const checks = document.querySelectorAll('.task-check');
        const allChecked = Array.from(checks).every(x => x.checked);
        const anyChecked = Array.from(checks).some(x => x.checked);

        if (checkAll) {
            checkAll.checked = allChecked;
            checkAll.indeterminate = anyChecked && !allChecked;
        }

        updateBulkSubmitState();
    });
});



const bulkAction = document.getElementById('bulkAction');
if (bulkAction) bulkAction.addEventListener('change', updateBulkSubmitState);

updateBulkSubmitState();
