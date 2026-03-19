/******/ (() => { // webpackBootstrap
/*!*******************************************!*\
  !*** ./resources/js/pages/tasks-index.js ***!
  \*******************************************/
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); } r ? i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2)); }, _regeneratorDefine2(e, r, n, t); }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
// ============================
// 初期状態：子タスクを閉じる
// ============================
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('tr[data-parent]').forEach(function (row) {
    row.style.display = 'none';
  });
});

// ============================
// アコーディオン（親子表示切替）
// ============================
document.addEventListener('click', function (e) {
  var btn = e.target.closest('.toggle-children');
  if (!btn) return;
  var parentId = btn.dataset.target;
  var children = document.querySelectorAll("tr[data-parent=\"".concat(parentId, "\"]"));
  if (children.length === 0) return;
  var isVisible = children[0].style.display !== 'none';
  if (isVisible) {
    children.forEach(function (child) {
      return closeRecursively(child);
    });

    // ボタンを ▶ に戻す
    btn.textContent = '▶';
  } else {
    children.forEach(function (child) {
      child.style.display = '';
    });

    // ボタンを ▼ に変更
    btn.textContent = '▼';
  }
});
function closeRecursively(row) {
  var id = row.dataset.id;
  row.style.display = 'none';
  var grandchildren = document.querySelectorAll("tr[data-parent=\"".concat(id, "\"]"));
  grandchildren.forEach(function (child) {
    return closeRecursively(child);
  });
}

// ============================
// CSRF helper
// ============================
function getCsrfToken() {
  var _document$querySelect;
  var token = (_document$querySelect = document.querySelector('meta[name="csrf-token"]')) === null || _document$querySelect === void 0 ? void 0 : _document$querySelect.getAttribute('content');
  if (!token) {
    console.error('CSRF token not found. Did you forget <meta name="csrf-token"> in layout?');
  }
  return token;
}
function postWithMethod(_x, _x2) {
  return _postWithMethod.apply(this, arguments);
} // ============================
// タスク操作ボタン
// ============================
function _postWithMethod() {
  _postWithMethod = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee2(url, method) {
    var csrf, body, res, message, contentType, json, text;
    return _regenerator().w(function (_context2) {
      while (1) switch (_context2.n) {
        case 0:
          csrf = getCsrfToken();
          if (csrf) {
            _context2.n = 1;
            break;
          }
          alert('CSRFトークンが見つかりません。layoutの<meta>を確認してください。');
          return _context2.a(2);
        case 1:
          body = new URLSearchParams();
          body.append('_token', csrf);
          body.append('_method', method);
          _context2.n = 2;
          return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: body
          });
        case 2:
          res = _context2.v;
          if (!res.ok) {
            _context2.n = 3;
            break;
          }
          location.reload();
          return _context2.a(2);
        case 3:
          message = '操作に失敗しました。';
          contentType = res.headers.get('content-type') || '';
          if (!contentType.includes('application/json')) {
            _context2.n = 5;
            break;
          }
          _context2.n = 4;
          return res.json()["catch"](function () {
            return null;
          });
        case 4:
          json = _context2.v;
          if (json !== null && json !== void 0 && json.message) message += '\n\n' + json.message;
          _context2.n = 7;
          break;
        case 5:
          _context2.n = 6;
          return res.text()["catch"](function () {
            return '';
          });
        case 6:
          text = _context2.v;
          if (text) message += '\n\n' + text.slice(0, 500);
        case 7:
          alert(message);
        case 8:
          return _context2.a(2);
      }
    }, _callee2);
  }));
  return _postWithMethod.apply(this, arguments);
}
document.addEventListener('click', /*#__PURE__*/function () {
  var _ref = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee(e) {
    var completeBtn, uncompleteBtn, deleteBtn;
    return _regenerator().w(function (_context) {
      while (1) switch (_context.n) {
        case 0:
          completeBtn = e.target.closest('.btn-complete');
          if (!completeBtn) {
            _context.n = 2;
            break;
          }
          _context.n = 1;
          return postWithMethod(completeBtn.dataset.url, 'PATCH');
        case 1:
          return _context.a(2);
        case 2:
          uncompleteBtn = e.target.closest('.btn-uncomplete');
          if (!uncompleteBtn) {
            _context.n = 4;
            break;
          }
          _context.n = 3;
          return postWithMethod(uncompleteBtn.dataset.url, 'PATCH');
        case 3:
          return _context.a(2);
        case 4:
          deleteBtn = e.target.closest('.btn-delete');
          if (!deleteBtn) {
            _context.n = 7;
            break;
          }
          if (confirm('このタスクを削除しますか？')) {
            _context.n = 5;
            break;
          }
          return _context.a(2);
        case 5:
          _context.n = 6;
          return postWithMethod(deleteBtn.dataset.url, 'DELETE');
        case 6:
          return _context.a(2);
        case 7:
          return _context.a(2);
      }
    }, _callee);
  }));
  return function (_x3) {
    return _ref.apply(this, arguments);
  };
}());

// ============================
// 一括操作（bulk）
// ============================
function updateBulkSubmitState() {
  var action = document.getElementById('bulkAction');
  var submit = document.getElementById('bulkSubmit');
  var checks = document.querySelectorAll('.task-check');
  if (!action || !submit || checks.length === 0) return;
  var anyChecked = Array.from(checks).some(function (ch) {
    return ch.checked;
  });
  var hasAction = action.value !== '';
  submit.disabled = !(anyChecked && hasAction);
}
function setAllChecks(checked) {
  var checks = document.querySelectorAll('.task-check');
  checks.forEach(function (ch) {
    return ch.checked = checked;
  });
}
var checkAll = document.getElementById('checkAll');
if (checkAll) {
  checkAll.addEventListener('change', function () {
    setAllChecks(checkAll.checked);
    updateBulkSubmitState();
  });
}
document.querySelectorAll('.task-check').forEach(function (ch) {
  ch.addEventListener('change', function () {
    var checks = document.querySelectorAll('.task-check');
    var allChecked = Array.from(checks).every(function (x) {
      return x.checked;
    });
    var anyChecked = Array.from(checks).some(function (x) {
      return x.checked;
    });
    if (checkAll) {
      checkAll.checked = allChecked;
      checkAll.indeterminate = anyChecked && !allChecked;
    }
    updateBulkSubmitState();
  });
});
var bulkAction = document.getElementById('bulkAction');
if (bulkAction) bulkAction.addEventListener('change', updateBulkSubmitState);
updateBulkSubmitState();
/******/ })()
;