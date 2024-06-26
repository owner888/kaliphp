"use strict";
!function(e, t) {
    "object" == typeof exports && "undefined" != typeof module ? t(exports) : "function" == typeof define && define.amd ? define(["exports"], t) : t((e = e || self).Popper = {})
}(this, (function(e) {
    function t(e) {
        return {
            width: (e = e.getBoundingClientRect()).width,
            height: e.height,
            top: e.top,
            right: e.right,
            bottom: e.bottom,
            left: e.left,
            x: e.left,
            y: e.top
        }
    }
    function n(e) {
        return "[object Window]" !== e.toString() ? (e = e.ownerDocument) && e.defaultView || window : e
    }
    function r(e) {
        return {
            scrollLeft: (e = n(e)).pageXOffset,
            scrollTop: e.pageYOffset
        }
    }
    function o(e) {
        return e instanceof n(e).Element || e instanceof Element
    }
    function i(e) {
        return e instanceof n(e).HTMLElement || e instanceof HTMLElement
    }
    function a(e) {
        return e ? (e.nodeName || "").toLowerCase() : null
    }
    function s(e) {
        return ((o(e) ? e.ownerDocument : e.document) || window.document).documentElement
    }
    function f(e) {
        return t(s(e)).left + r(e).scrollLeft
    }
    function c(e) {
        return n(e).getComputedStyle(e)
    }
    function p(e) {
        return e = c(e),
        /auto|scroll|overlay|hidden/.test(e.overflow + e.overflowY + e.overflowX)
    }
    function l(e, o, c) {
        void 0 === c && (c = !1);
        var l = s(o);
        e = t(e);
        var u = i(o)
          , d = {
            scrollLeft: 0,
            scrollTop: 0
        }
          , m = {
            x: 0,
            y: 0
        };
        return (u || !u && !c) && (("body" !== a(o) || p(l)) && (d = o !== n(o) && i(o) ? {
            scrollLeft: o.scrollLeft,
            scrollTop: o.scrollTop
        } : r(o)),
        i(o) ? ((m = t(o)).x += o.clientLeft,
        m.y += o.clientTop) : l && (m.x = f(l))),
        {
            x: e.left + d.scrollLeft - m.x,
            y: e.top + d.scrollTop - m.y,
            width: e.width,
            height: e.height
        }
    }
    function u(e) {
        return {
            x: e.offsetLeft,
            y: e.offsetTop,
            width: e.offsetWidth,
            height: e.offsetHeight
        }
    }
    function d(e) {
        return "html" === a(e) ? e : e.assignedSlot || e.parentNode || e.host || s(e)
    }
    function m(e, t) {
        void 0 === t && (t = []);
        var r = function e(t) {
            return 0 <= ["html", "body", "#document"].indexOf(a(t)) ? t.ownerDocument.body : i(t) && p(t) ? t : e(d(t))
        }(e);
        e = "body" === a(r);
        var o = n(r);
        return r = e ? [o].concat(o.visualViewport || [], p(r) ? r : []) : r,
        t = t.concat(r),
        e ? t : t.concat(m(d(r)))
    }
    function h(e) {
        if (!i(e) || "fixed" === c(e).position)
            return null;
        if (e = e.offsetParent) {
            var t = s(e);
            if ("body" === a(e) && "static" === c(e).position && "static" !== c(t).position)
                return t
        }
        return e
    }
    function g(e) {
        for (var t = n(e), r = h(e); r && 0 <= ["table", "td", "th"].indexOf(a(r)) && "static" === c(r).position; )
            r = h(r);
        if (r && "body" === a(r) && "static" === c(r).position)
            return t;
        if (!r)
            e: {
                for (e = d(e); i(e) && 0 > ["html", "body"].indexOf(a(e)); ) {
                    if ("none" !== (r = c(e)).transform || "none" !== r.perspective || r.willChange && "auto" !== r.willChange) {
                        r = e;
                        break e
                    }
                    e = e.parentNode
                }
                r = null
            }
        return r || t
    }
    function v(e) {
        var t = new Map
          , n = new Set
          , r = [];
        return e.forEach((function(e) {
            t.set(e.name, e)
        }
        )),
        e.forEach((function(e) {
            n.has(e.name) || function e(o) {
                n.add(o.name),
                [].concat(o.requires || [], o.requiresIfExists || []).forEach((function(r) {
                    n.has(r) || (r = t.get(r)) && e(r)
                }
                )),
                r.push(o)
            }(e)
        }
        )),
        r
    }
    function b(e) {
        var t;
        return function() {
            return t || (t = new Promise((function(n) {
                Promise.resolve().then((function() {
                    t = void 0,
                    n(e())
                }
                ))
            }
            ))),
            t
        }
    }
    function y(e) {
        return e.split("-")[0]
    }
    function O(e, t) {
        var r, o = t.getRootNode && t.getRootNode();
        if (e.contains(t))
            return !0;
        if ((r = o) && (r = o instanceof (r = n(o).ShadowRoot) || o instanceof ShadowRoot),
        r)
            do {
                if (t && e.isSameNode(t))
                    return !0;
                t = t.parentNode || t.host
            } while (t);
        return !1
    }
    function w(e) {
        return Object.assign(Object.assign({}, e), {}, {
            left: e.x,
            top: e.y,
            right: e.x + e.width,
            bottom: e.y + e.height
        })
    }
    function x(e, o) {
        if ("viewport" === o) {
            o = n(e);
            var a = s(e);
            o = o.visualViewport;
            var p = a.clientWidth;
            a = a.clientHeight;
            var l = 0
              , u = 0;
            o && (p = o.width,
            a = o.height,
            /^((?!chrome|android).)*safari/i.test(navigator.userAgent) || (l = o.offsetLeft,
            u = o.offsetTop)),
            e = w(e = {
                width: p,
                height: a,
                x: l + f(e),
                y: u
            })
        } else
            i(o) ? ((e = t(o)).top += o.clientTop,
            e.left += o.clientLeft,
            e.bottom = e.top + o.clientHeight,
            e.right = e.left + o.clientWidth,
            e.width = o.clientWidth,
            e.height = o.clientHeight,
            e.x = e.left,
            e.y = e.top) : (u = s(e),
            e = s(u),
            l = r(u),
            o = u.ownerDocument.body,
            p = Math.max(e.scrollWidth, e.clientWidth, o ? o.scrollWidth : 0, o ? o.clientWidth : 0),
            a = Math.max(e.scrollHeight, e.clientHeight, o ? o.scrollHeight : 0, o ? o.clientHeight : 0),
            u = -l.scrollLeft + f(u),
            l = -l.scrollTop,
            "rtl" === c(o || e).direction && (u += Math.max(e.clientWidth, o ? o.clientWidth : 0) - p),
            e = w({
                width: p,
                height: a,
                x: u,
                y: l
            }));
        return e
    }
    function j(e, t, n) {
        return t = "clippingParents" === t ? function(e) {
            var t = m(d(e))
              , n = 0 <= ["absolute", "fixed"].indexOf(c(e).position) && i(e) ? g(e) : e;
            return o(n) ? t.filter((function(e) {
                return o(e) && O(e, n) && "body" !== a(e)
            }
            )) : []
        }(e) : [].concat(t),
        (n = (n = [].concat(t, [n])).reduce((function(t, n) {
            return n = x(e, n),
            t.top = Math.max(n.top, t.top),
            t.right = Math.min(n.right, t.right),
            t.bottom = Math.min(n.bottom, t.bottom),
            t.left = Math.max(n.left, t.left),
            t
        }
        ), x(e, n[0]))).width = n.right - n.left,
        n.height = n.bottom - n.top,
        n.x = n.left,
        n.y = n.top,
        n
    }
    function M(e) {
        return 0 <= ["top", "bottom"].indexOf(e) ? "x" : "y"
    }
    function E(e) {
        var t = e.reference
          , n = e.element
          , r = (e = e.placement) ? y(e) : null;
        e = e ? e.split("-")[1] : null;
        var o = t.x + t.width / 2 - n.width / 2
          , i = t.y + t.height / 2 - n.height / 2;
        switch (r) {
        case "top":
            o = {
                x: o,
                y: t.y - n.height
            };
            break;
        case "bottom":
            o = {
                x: o,
                y: t.y + t.height
            };
            break;
        case "right":
            o = {
                x: t.x + t.width,
                y: i
            };
            break;
        case "left":
            o = {
                x: t.x - n.width,
                y: i
            };
            break;
        default:
            o = {
                x: t.x,
                y: t.y
            }
        }
        if (null != (r = r ? M(r) : null))
            switch (i = "y" === r ? "height" : "width",
            e) {
            case "start":
                o[r] = Math.floor(o[r]) - Math.floor(t[i] / 2 - n[i] / 2);
                break;
            case "end":
                o[r] = Math.floor(o[r]) + Math.ceil(t[i] / 2 - n[i] / 2)
            }
        return o
    }
    function D(e) {
        return Object.assign(Object.assign({}, {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0
        }), e)
    }
    function P(e, t) {
        return t.reduce((function(t, n) {
            return t[n] = e,
            t
        }
        ), {})
    }
    function L(e, n) {
        void 0 === n && (n = {});
        var r = n;
        n = void 0 === (n = r.placement) ? e.placement : n;
        var i = r.boundary
          , a = void 0 === i ? "clippingParents" : i
          , f = void 0 === (i = r.rootBoundary) ? "viewport" : i;
        i = void 0 === (i = r.elementContext) ? "popper" : i;
        var c = r.altBoundary
          , p = void 0 !== c && c;
        r = D("number" != typeof (r = void 0 === (r = r.padding) ? 0 : r) ? r : P(r, T));
        var l = e.elements.reference;
        c = e.rects.popper,
        a = j(o(p = e.elements[p ? "popper" === i ? "reference" : "popper" : i]) ? p : p.contextElement || s(e.elements.popper), a, f),
        p = E({
            reference: f = t(l),
            element: c,
            strategy: "absolute",
            placement: n
        }),
        c = w(Object.assign(Object.assign({}, c), p)),
        f = "popper" === i ? c : f;
        var u = {
            top: a.top - f.top + r.top,
            bottom: f.bottom - a.bottom + r.bottom,
            left: a.left - f.left + r.left,
            right: f.right - a.right + r.right
        };
        if (e = e.modifiersData.offset,
        "popper" === i && e) {
            var d = e[n];
            Object.keys(u).forEach((function(e) {
                var t = 0 <= ["right", "bottom"].indexOf(e) ? 1 : -1
                  , n = 0 <= ["top", "bottom"].indexOf(e) ? "y" : "x";
                u[e] += d[n] * t
            }
            ))
        }
        return u
    }
    function k() {
        for (var e = arguments.length, t = Array(e), n = 0; n < e; n++)
            t[n] = arguments[n];
        return !t.some((function(e) {
            return !(e && "function" == typeof e.getBoundingClientRect)
        }
        ))
    }
    function B(e) {
        void 0 === e && (e = {});
        var t = e.defaultModifiers
          , n = void 0 === t ? [] : t
          , r = void 0 === (e = e.defaultOptions) ? V : e;
        return function(e, t, i) {
            function a() {
                f.forEach((function(e) {
                    return e()
                }
                )),
                f = []
            }
            void 0 === i && (i = r);
            var s = {
                placement: "bottom",
                orderedModifiers: [],
                options: Object.assign(Object.assign({}, V), r),
                modifiersData: {},
                elements: {
                    reference: e,
                    popper: t
                },
                attributes: {},
                styles: {}
            }
              , f = []
              , c = !1
              , p = {
                state: s,
                setOptions: function(i) {
                    return a(),
                    s.options = Object.assign(Object.assign(Object.assign({}, r), s.options), i),
                    s.scrollParents = {
                        reference: o(e) ? m(e) : e.contextElement ? m(e.contextElement) : [],
                        popper: m(t)
                    },
                    i = function(e) {
                        var t = v(e);
                        return N.reduce((function(e, n) {
                            return e.concat(t.filter((function(e) {
                                return e.phase === n
                            }
                            )))
                        }
                        ), [])
                    }(function(e) {
                        var t = e.reduce((function(e, t) {
                            var n = e[t.name];
                            return e[t.name] = n ? Object.assign(Object.assign(Object.assign({}, n), t), {}, {
                                options: Object.assign(Object.assign({}, n.options), t.options),
                                data: Object.assign(Object.assign({}, n.data), t.data)
                            }) : t,
                            e
                        }
                        ), {});
                        return Object.keys(t).map((function(e) {
                            return t[e]
                        }
                        ))
                    }([].concat(n, s.options.modifiers))),
                    s.orderedModifiers = i.filter((function(e) {
                        return e.enabled
                    }
                    )),
                    s.orderedModifiers.forEach((function(e) {
                        var t = e.name
                          , n = e.options;
                        n = void 0 === n ? {} : n,
                        "function" == typeof (e = e.effect) && (t = e({
                            state: s,
                            name: t,
                            instance: p,
                            options: n
                        }),
                        f.push(t || function() {}
                        ))
                    }
                    )),
                    p.update()
                },
                forceUpdate: function() {
                    if (!c) {
                        var e = s.elements
                          , t = e.reference;
                        if (k(t, e = e.popper))
                            for (s.rects = {
                                reference: l(t, g(e), "fixed" === s.options.strategy),
                                popper: u(e)
                            },
                            s.reset = !1,
                            s.placement = s.options.placement,
                            s.orderedModifiers.forEach((function(e) {
                                return s.modifiersData[e.name] = Object.assign({}, e.data)
                            }
                            )),
                            t = 0; t < s.orderedModifiers.length; t++)
                                if (!0 === s.reset)
                                    s.reset = !1,
                                    t = -1;
                                else {
                                    var n = s.orderedModifiers[t];
                                    e = n.fn;
                                    var r = n.options;
                                    r = void 0 === r ? {} : r,
                                    n = n.name,
                                    "function" == typeof e && (s = e({
                                        state: s,
                                        options: r,
                                        name: n,
                                        instance: p
                                    }) || s)
                                }
                    }
                },
                update: b((function() {
                    return new Promise((function(e) {
                        p.forceUpdate(),
                        e(s)
                    }
                    ))
                }
                )),
                destroy: function() {
                    a(),
                    c = !0
                }
            };
            return k(e, t) ? (p.setOptions(i).then((function(e) {
                !c && i.onFirstUpdate && i.onFirstUpdate(e)
            }
            )),
            p) : p
        }
    }
    function W(e) {
        var t, r = e.popper, o = e.popperRect, i = e.placement, a = e.offsets, f = e.position, c = e.gpuAcceleration, p = e.adaptive, l = window.devicePixelRatio || 1;
        e = Math.round(a.x * l) / l || 0,
        l = Math.round(a.y * l) / l || 0;
        var u = a.hasOwnProperty("x");
        a = a.hasOwnProperty("y");
        var d, m = "left", h = "top", v = window;
        if (p) {
            var b = g(r);
            b === n(r) && (b = s(r)),
            "top" === i && (h = "bottom",
            l -= b.clientHeight - o.height,
            l *= c ? 1 : -1),
            "left" === i && (m = "right",
            e -= b.clientWidth - o.width,
            e *= c ? 1 : -1)
        }
        return r = Object.assign({
            position: f
        }, p && z),
        c ? Object.assign(Object.assign({}, r), {}, ((d = {})[h] = a ? "0" : "",
        d[m] = u ? "0" : "",
        d.transform = 2 > (v.devicePixelRatio || 1) ? "translate(" + e + "px, " + l + "px)" : "translate3d(" + e + "px, " + l + "px, 0)",
        d)) : Object.assign(Object.assign({}, r), {}, ((t = {})[h] = a ? l + "px" : "",
        t[m] = u ? e + "px" : "",
        t.transform = "",
        t))
    }
    function A(e) {
        return e.replace(/left|right|bottom|top/g, (function(e) {
            return G[e]
        }
        ))
    }
    function H(e) {
        return e.replace(/start|end/g, (function(e) {
            return J[e]
        }
        ))
    }
    function R(e, t, n) {
        return void 0 === n && (n = {
            x: 0,
            y: 0
        }),
        {
            top: e.top - t.height - n.y,
            right: e.right - t.width + n.x,
            bottom: e.bottom - t.height + n.y,
            left: e.left - t.width - n.x
        }
    }
    function S(e) {
        return ["top", "right", "bottom", "left"].some((function(t) {
            return 0 <= e[t]
        }
        ))
    }
    var T = ["top", "bottom", "right", "left"]
      , q = T.reduce((function(e, t) {
        return e.concat([t + "-start", t + "-end"])
    }
    ), [])
      , C = [].concat(T, ["auto"]).reduce((function(e, t) {
        return e.concat([t, t + "-start", t + "-end"])
    }
    ), [])
      , N = "beforeRead read afterRead beforeMain main afterMain beforeWrite write afterWrite".split(" ")
      , V = {
        placement: "bottom",
        modifiers: [],
        strategy: "absolute"
    }
      , I = {
        passive: !0
    }
      , _ = {
        name: "eventListeners",
        enabled: !0,
        phase: "write",
        fn: function() {},
        effect: function(e) {
            var t = e.state
              , r = e.instance
              , o = (e = e.options).scroll
              , i = void 0 === o || o
              , a = void 0 === (e = e.resize) || e
              , s = n(t.elements.popper)
              , f = [].concat(t.scrollParents.reference, t.scrollParents.popper);
            return i && f.forEach((function(e) {
                e.addEventListener("scroll", r.update, I)
            }
            )),
            a && s.addEventListener("resize", r.update, I),
            function() {
                i && f.forEach((function(e) {
                    e.removeEventListener("scroll", r.update, I)
                }
                )),
                a && s.removeEventListener("resize", r.update, I)
            }
        },
        data: {}
    }
      , U = {
        name: "popperOffsets",
        enabled: !0,
        phase: "read",
        fn: function(e) {
            var t = e.state;
            t.modifiersData[e.name] = E({
                reference: t.rects.reference,
                element: t.rects.popper,
                strategy: "absolute",
                placement: t.placement
            })
        },
        data: {}
    }
      , z = {
        top: "auto",
        right: "auto",
        bottom: "auto",
        left: "auto"
    }
      , F = {
        name: "computeStyles",
        enabled: !0,
        phase: "beforeWrite",
        fn: function(e) {
            var t = e.state
              , n = e.options;
            e = void 0 === (e = n.gpuAcceleration) || e,
            n = void 0 === (n = n.adaptive) || n,
            e = {
                placement: y(t.placement),
                popper: t.elements.popper,
                popperRect: t.rects.popper,
                gpuAcceleration: e
            },
            null != t.modifiersData.popperOffsets && (t.styles.popper = Object.assign(Object.assign({}, t.styles.popper), W(Object.assign(Object.assign({}, e), {}, {
                offsets: t.modifiersData.popperOffsets,
                position: t.options.strategy,
                adaptive: n
            })))),
            null != t.modifiersData.arrow && (t.styles.arrow = Object.assign(Object.assign({}, t.styles.arrow), W(Object.assign(Object.assign({}, e), {}, {
                offsets: t.modifiersData.arrow,
                position: "absolute",
                adaptive: !1
            })))),
            t.attributes.popper = Object.assign(Object.assign({}, t.attributes.popper), {}, {
                "data-popper-placement": t.placement
            })
        },
        data: {}
    }
      , X = {
        name: "applyStyles",
        enabled: !0,
        phase: "write",
        fn: function(e) {
            var t = e.state;
            Object.keys(t.elements).forEach((function(e) {
                var n = t.styles[e] || {}
                  , r = t.attributes[e] || {}
                  , o = t.elements[e];
                i(o) && a(o) && (Object.assign(o.style, n),
                Object.keys(r).forEach((function(e) {
                    var t = r[e];
                    !1 === t ? o.removeAttribute(e) : o.setAttribute(e, !0 === t ? "" : t)
                }
                )))
            }
            ))
        },
        effect: function(e) {
            var t = e.state
              , n = {
                popper: {
                    position: t.options.strategy,
                    left: "0",
                    top: "0",
                    margin: "0"
                },
                arrow: {
                    position: "absolute"
                },
                reference: {}
            };
            return Object.assign(t.elements.popper.style, n.popper),
            t.elements.arrow && Object.assign(t.elements.arrow.style, n.arrow),
            function() {
                Object.keys(t.elements).forEach((function(e) {
                    var r = t.elements[e]
                      , o = t.attributes[e] || {};
                    e = Object.keys(t.styles.hasOwnProperty(e) ? t.styles[e] : n[e]).reduce((function(e, t) {
                        return e[t] = "",
                        e
                    }
                    ), {}),
                    i(r) && a(r) && (Object.assign(r.style, e),
                    Object.keys(o).forEach((function(e) {
                        r.removeAttribute(e)
                    }
                    )))
                }
                ))
            }
        },
        requires: ["computeStyles"]
    }
      , Y = {
        name: "offset",
        enabled: !0,
        phase: "main",
        requires: ["popperOffsets"],
        fn: function(e) {
            var t = e.state
              , n = e.name
              , r = void 0 === (e = e.options.offset) ? [0, 0] : e
              , o = (e = C.reduce((function(e, n) {
                var o = t.rects
                  , i = y(n)
                  , a = 0 <= ["left", "top"].indexOf(i) ? -1 : 1
                  , s = "function" == typeof r ? r(Object.assign(Object.assign({}, o), {}, {
                    placement: n
                })) : r;
                return o = (o = s[0]) || 0,
                s = ((s = s[1]) || 0) * a,
                i = 0 <= ["left", "right"].indexOf(i) ? {
                    x: s,
                    y: o
                } : {
                    x: o,
                    y: s
                },
                e[n] = i,
                e
            }
            ), {}))[t.placement]
              , i = o.x;
            o = o.y,
            null != t.modifiersData.popperOffsets && (t.modifiersData.popperOffsets.x += i,
            t.modifiersData.popperOffsets.y += o),
            t.modifiersData[n] = e
        }
    }
      , G = {
        left: "right",
        right: "left",
        bottom: "top",
        top: "bottom"
    }
      , J = {
        start: "end",
        end: "start"
    }
      , K = {
        name: "flip",
        enabled: !0,
        phase: "main",
        fn: function(e) {
            var t = e.state
              , n = e.options;
            if (e = e.name,
            !t.modifiersData[e]._skip) {
                var r = n.mainAxis;
                r = void 0 === r || r;
                var o = n.altAxis;
                o = void 0 === o || o;
                var i = n.fallbackPlacements
                  , a = n.padding
                  , s = n.boundary
                  , f = n.rootBoundary
                  , c = n.altBoundary
                  , p = n.flipVariations
                  , l = void 0 === p || p
                  , u = n.allowedAutoPlacements;
                p = y(n = t.options.placement),
                i = i || (p !== n && l ? function(e) {
                    if ("auto" === y(e))
                        return [];
                    var t = A(e);
                    return [H(e), t, H(t)]
                }(n) : [A(n)]);
                var d = [n].concat(i).reduce((function(e, n) {
                    return e.concat("auto" === y(n) ? function(e, t) {
                        void 0 === t && (t = {});
                        var n = t.boundary
                          , r = t.rootBoundary
                          , o = t.padding
                          , i = t.flipVariations
                          , a = t.allowedAutoPlacements
                          , s = void 0 === a ? C : a
                          , f = t.placement.split("-")[1];
                        0 === (i = (t = f ? i ? q : q.filter((function(e) {
                            return e.split("-")[1] === f
                        }
                        )) : T).filter((function(e) {
                            return 0 <= s.indexOf(e)
                        }
                        ))).length && (i = t);
                        var c = i.reduce((function(t, i) {
                            return t[i] = L(e, {
                                placement: i,
                                boundary: n,
                                rootBoundary: r,
                                padding: o
                            })[y(i)],
                            t
                        }
                        ), {});
                        return Object.keys(c).sort((function(e, t) {
                            return c[e] - c[t]
                        }
                        ))
                    }(t, {
                        placement: n,
                        boundary: s,
                        rootBoundary: f,
                        padding: a,
                        flipVariations: l,
                        allowedAutoPlacements: u
                    }) : n)
                }
                ), []);
                n = t.rects.reference,
                i = t.rects.popper;
                var m = new Map;
                p = !0;
                for (var h = d[0], g = 0; g < d.length; g++) {
                    var v = d[g]
                      , b = y(v)
                      , O = "start" === v.split("-")[1]
                      , w = 0 <= ["top", "bottom"].indexOf(b)
                      , x = w ? "width" : "height"
                      , j = L(t, {
                        placement: v,
                        boundary: s,
                        rootBoundary: f,
                        altBoundary: c,
                        padding: a
                    });
                    if (O = w ? O ? "right" : "left" : O ? "bottom" : "top",
                    n[x] > i[x] && (O = A(O)),
                    x = A(O),
                    w = [],
                    r && w.push(0 >= j[b]),
                    o && w.push(0 >= j[O], 0 >= j[x]),
                    w.every((function(e) {
                        return e
                    }
                    ))) {
                        h = v,
                        p = !1;
                        break
                    }
                    m.set(v, w)
                }
                if (p)
                    for (r = function(e) {
                        var t = d.find((function(t) {
                            if (t = m.get(t))
                                return t.slice(0, e).every((function(e) {
                                    return e
                                }
                                ))
                        }
                        ));
                        if (t)
                            return h = t,
                            "break"
                    }
                    ,
                    o = l ? 3 : 1; 0 < o && "break" !== r(o); o--)
                        ;
                t.placement !== h && (t.modifiersData[e]._skip = !0,
                t.placement = h,
                t.reset = !0)
            }
        },
        requiresIfExists: ["offset"],
        data: {
            _skip: !1
        }
    }
      , Q = {
        name: "preventOverflow",
        enabled: !0,
        phase: "main",
        fn: function(e) {
            var t = e.state
              , n = e.options;
            e = e.name;
            var r = n.mainAxis
              , o = void 0 === r || r;
            r = void 0 !== (r = n.altAxis) && r;
            var i = n.tether;
            i = void 0 === i || i;
            var a = n.tetherOffset
              , s = void 0 === a ? 0 : a;
            n = L(t, {
                boundary: n.boundary,
                rootBoundary: n.rootBoundary,
                padding: n.padding,
                altBoundary: n.altBoundary
            }),
            a = y(t.placement);
            var f = t.placement.split("-")[1]
              , c = !f
              , p = M(a);
            a = "x" === p ? "y" : "x";
            var l = t.modifiersData.popperOffsets
              , d = t.rects.reference
              , m = t.rects.popper
              , h = "function" == typeof s ? s(Object.assign(Object.assign({}, t.rects), {}, {
                placement: t.placement
            })) : s;
            if (s = {
                x: 0,
                y: 0
            },
            l) {
                if (o) {
                    var v = "y" === p ? "top" : "left"
                      , b = "y" === p ? "bottom" : "right"
                      , O = "y" === p ? "height" : "width";
                    o = l[p];
                    var w = l[p] + n[v]
                      , x = l[p] - n[b]
                      , j = i ? -m[O] / 2 : 0
                      , E = "start" === f ? d[O] : m[O];
                    f = "start" === f ? -m[O] : -d[O],
                    m = t.elements.arrow,
                    m = i && m ? u(m) : {
                        width: 0,
                        height: 0
                    };
                    var D = t.modifiersData["arrow#persistent"] ? t.modifiersData["arrow#persistent"].padding : {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    };
                    v = D[v],
                    b = D[b],
                    m = Math.max(0, Math.min(d[O], m[O])),
                    E = c ? d[O] / 2 - j - m - v - h : E - m - v - h,
                    c = c ? -d[O] / 2 + j + m + b + h : f + m + b + h,
                    h = t.elements.arrow && g(t.elements.arrow),
                    d = t.modifiersData.offset ? t.modifiersData.offset[t.placement][p] : 0,
                    h = l[p] + E - d - (h ? "y" === p ? h.clientTop || 0 : h.clientLeft || 0 : 0),
                    c = l[p] + c - d,
                    i = Math.max(i ? Math.min(w, h) : w, Math.min(o, i ? Math.max(x, c) : x)),
                    l[p] = i,
                    s[p] = i - o
                }
                r && (r = l[a],
                i = Math.max(r + n["x" === p ? "top" : "left"], Math.min(r, r - n["x" === p ? "bottom" : "right"])),
                l[a] = i,
                s[a] = i - r),
                t.modifiersData[e] = s
            }
        },
        requiresIfExists: ["offset"]
    }
      , Z = {
        name: "arrow",
        enabled: !0,
        phase: "main",
        fn: function(e) {
            var t, n = e.state;
            e = e.name;
            var r = n.elements.arrow
              , o = n.modifiersData.popperOffsets
              , i = y(n.placement)
              , a = M(i);
            if (i = 0 <= ["left", "right"].indexOf(i) ? "height" : "width",
            r && o) {
                var s = n.modifiersData[e + "#persistent"].padding
                  , f = u(r)
                  , c = "y" === a ? "top" : "left"
                  , p = "y" === a ? "bottom" : "right"
                  , l = n.rects.reference[i] + n.rects.reference[a] - o[a] - n.rects.popper[i];
                o = o[a] - n.rects.reference[a],
                l = (r = (r = g(r)) ? "y" === a ? r.clientHeight || 0 : r.clientWidth || 0 : 0) / 2 - f[i] / 2 + (l / 2 - o / 2),
                i = Math.max(s[c], Math.min(l, r - f[i] - s[p])),
                n.modifiersData[e] = ((t = {})[a] = i,
                t.centerOffset = i - l,
                t)
            }
        },
        effect: function(e) {
            var t = e.state
              , n = e.options;
            e = e.name;
            var r = n.element;
            if (r = void 0 === r ? "[data-popper-arrow]" : r,
            n = void 0 === (n = n.padding) ? 0 : n,
            null != r) {
                if ("string" == typeof r && !(r = t.elements.popper.querySelector(r)))
                    return;
                O(t.elements.popper, r) && (t.elements.arrow = r,
                t.modifiersData[e + "#persistent"] = {
                    padding: D("number" != typeof n ? n : P(n, T))
                })
            }
        },
        requires: ["popperOffsets"],
        requiresIfExists: ["preventOverflow"]
    }
      , $ = {
        name: "hide",
        enabled: !0,
        phase: "main",
        requiresIfExists: ["preventOverflow"],
        fn: function(e) {
            var t = e.state;
            e = e.name;
            var n = t.rects.reference
              , r = t.rects.popper
              , o = t.modifiersData.preventOverflow
              , i = L(t, {
                elementContext: "reference"
            })
              , a = L(t, {
                altBoundary: !0
            });
            n = R(i, n),
            r = R(a, r, o),
            o = S(n),
            a = S(r),
            t.modifiersData[e] = {
                referenceClippingOffsets: n,
                popperEscapeOffsets: r,
                isReferenceHidden: o,
                hasPopperEscaped: a
            },
            t.attributes.popper = Object.assign(Object.assign({}, t.attributes.popper), {}, {
                "data-popper-reference-hidden": o,
                "data-popper-escaped": a
            })
        }
    }
      , ee = B({
        defaultModifiers: [_, U, F, X]
    })
      , te = [_, U, F, X, Y, K, Q, Z, $]
      , ne = B({
        defaultModifiers: te
    });
    e.applyStyles = X,
    e.arrow = Z,
    e.computeStyles = F,
    e.createPopper = ne,
    e.createPopperLite = ee,
    e.defaultModifiers = te,
    e.detectOverflow = L,
    e.eventListeners = _,
    e.flip = K,
    e.hide = $,
    e.offset = Y,
    e.popperGenerator = B,
    e.popperOffsets = U,
    e.preventOverflow = Q,
    Object.defineProperty(e, "__esModule", {
        value: !0
    })
}
));
!function(t, e) {
    "object" == typeof exports && "undefined" != typeof module ? module.exports = e(require("@popperjs/core")) : "function" == typeof define && define.amd ? define(["@popperjs/core"], e) : (t = t || self).tippy = e(t.Popper)
}(this, (function(t) {
    "use strict";
    var e = "undefined" != typeof window && "undefined" != typeof document
      , n = e ? navigator.userAgent : ""
      , r = /MSIE |Trident\//.test(n)
      , i = {
        passive: !0,
        capture: !0
    };
    function o(t, e, n) {
        if (Array.isArray(t)) {
            var r = t[e];
            return null == r ? Array.isArray(n) ? n[e] : n : r
        }
        return t
    }
    function a(t, e) {
        var n = {}.toString.call(t);
        return 0 === n.indexOf("[object") && n.indexOf(e + "]") > -1
    }
    function s(t, e) {
        return "function" == typeof t ? t.apply(void 0, e) : t
    }
    function u(t, e) {
        return 0 === e ? t : function(r) {
            clearTimeout(n),
            n = setTimeout((function() {
                t(r)
            }
            ), e)
        }
        ;
        var n
    }
    function c(t, e) {
        var n = Object.assign({}, t);
        return e.forEach((function(t) {
            delete n[t]
        }
        )),
        n
    }
    function p(t) {
        return [].concat(t)
    }
    function f(t, e) {
        -1 === t.indexOf(e) && t.push(e)
    }
    function l(t) {
        return t.split("-")[0]
    }
    function d(t) {
        return [].slice.call(t)
    }
    function v() {
        return document.createElement("div")
    }
    function m(t) {
        return ["Element", "Fragment"].some((function(e) {
            return a(t, e)
        }
        ))
    }
    function g(t) {
        return a(t, "MouseEvent")
    }
    function h(t) {
        return !(!t || !t._tippy || t._tippy.reference !== t)
    }
    function b(t) {
        return m(t) ? [t] : function(t) {
            return a(t, "NodeList")
        }(t) ? d(t) : Array.isArray(t) ? t : d(document.querySelectorAll(t))
    }
    function y(t, e) {
        t.forEach((function(t) {
            t && (t.style.transitionDuration = e + "ms")
        }
        ))
    }
    function x(t, e) {
        t.forEach((function(t) {
            t && t.setAttribute("data-state", e)
        }
        ))
    }
    function w(t) {
        var e = p(t)[0];
        return e && e.ownerDocument || document
    }
    function E(t, e, n) {
        var r = e + "EventListener";
        ["transitionend", "webkitTransitionEnd"].forEach((function(e) {
            t[r](e, n)
        }
        ))
    }
    var T = {
        isTouch: !1
    }
      , C = 0;
    function A() {
        T.isTouch || (T.isTouch = !0,
        window.performance && document.addEventListener("mousemove", O))
    }
    function O() {
        var t = performance.now();
        t - C < 20 && (T.isTouch = !1,
        document.removeEventListener("mousemove", O)),
        C = t
    }
    function L() {
        var t = document.activeElement;
        if (h(t)) {
            var e = t._tippy;
            t.blur && !e.state.isVisible && t.blur()
        }
    }
    var D = Object.assign({
        appendTo: function() {
            return document.body
        },
        aria: {
            content: "auto",
            expanded: "auto"
        },
        delay: 0,
        duration: [300, 250],
        getReferenceClientRect: null,
        hideOnClick: !0,
        ignoreAttributes: !1,
        interactive: !1,
        interactiveBorder: 2,
        interactiveDebounce: 0,
        moveTransition: "",
        offset: [0, 10],
        onAfterUpdate: function() {},
        onBeforeUpdate: function() {},
        onCreate: function() {},
        onDestroy: function() {},
        onHidden: function() {},
        onHide: function() {},
        onMount: function() {},
        onShow: function() {},
        onShown: function() {},
        onTrigger: function() {},
        onUntrigger: function() {},
        onClickOutside: function() {},
        placement: "top",
        plugins: [],
        popperOptions: {},
        render: null,
        showOnCreate: !1,
        touch: !0,
        trigger: "mouseenter focus",
        triggerTarget: null
    }, {
        animateFill: !1,
        followCursor: !1,
        inlinePositioning: !1,
        sticky: !1
    }, {}, {
        allowHTML: !1,
        animation: "fade",
        arrow: !0,
        content: "",
        inertia: !1,
        maxWidth: 350,
        role: "tooltip",
        theme: "",
        zIndex: 9999
    })
      , k = Object.keys(D);
    function R(t) {
        var e = (t.plugins || []).reduce((function(e, n) {
            var r = n.name
              , i = n.defaultValue;
            return r && (e[r] = void 0 !== t[r] ? t[r] : i),
            e
        }
        ), {});
        return Object.assign({}, t, {}, e)
    }
    function M(t, e) {
        var n = Object.assign({}, e, {
            content: s(e.content, [t])
        }, e.ignoreAttributes ? {} : function(t, e) {
            return (e ? Object.keys(R(Object.assign({}, D, {
                plugins: e
            }))) : k).reduce((function(e, n) {
                var r = (t.getAttribute("data-tippy-" + n) || "").trim();
                if (!r)
                    return e;
                if ("content" === n)
                    e[n] = r;
                else
                    try {
                        e[n] = JSON.parse(r)
                    } catch (t) {
                        e[n] = r
                    }
                return e
            }
            ), {})
        }(t, e.plugins));
        return n.aria = Object.assign({}, D.aria, {}, n.aria),
        n.aria = {
            expanded: "auto" === n.aria.expanded ? e.interactive : n.aria.expanded,
            content: "auto" === n.aria.content ? e.interactive ? null : "describedby" : n.aria.content
        },
        n
    }
    function P(t, e) {
        t.innerHTML = e
    }
    function V(t) {
        var e = v();
        return !0 === t ? e.className = "tippy-arrow" : (e.className = "tippy-svg-arrow",
        m(t) ? e.appendChild(t) : P(e, t)),
        e
    }
    function j(t, e) {
        m(e.content) ? (P(t, ""),
        t.appendChild(e.content)) : "function" != typeof e.content && (e.allowHTML ? P(t, e.content) : t.textContent = e.content)
    }
    function I(t) {
        var e = t.firstElementChild
          , n = d(e.children);
        return {
            box: e,
            content: n.find((function(t) {
                return t.classList.contains("tippy-content")
            }
            )),
            arrow: n.find((function(t) {
                return t.classList.contains("tippy-arrow") || t.classList.contains("tippy-svg-arrow")
            }
            )),
            backdrop: n.find((function(t) {
                return t.classList.contains("tippy-backdrop")
            }
            ))
        }
    }
    function S(t) {
        var e = v()
          , n = v();
        n.className = "tippy-box",
        n.setAttribute("data-state", "hidden"),
        n.setAttribute("tabindex", "-1");
        var r = v();
        function i(n, r) {
            var i = I(e)
              , o = i.box
              , a = i.content
              , s = i.arrow;
            r.theme ? o.setAttribute("data-theme", r.theme) : o.removeAttribute("data-theme"),
            "string" == typeof r.animation ? o.setAttribute("data-animation", r.animation) : o.removeAttribute("data-animation"),
            r.inertia ? o.setAttribute("data-inertia", "") : o.removeAttribute("data-inertia"),
            o.style.maxWidth = "number" == typeof r.maxWidth ? r.maxWidth + "px" : r.maxWidth,
            r.role ? o.setAttribute("role", r.role) : o.removeAttribute("role"),
            n.content === r.content && n.allowHTML === r.allowHTML || j(a, t.props),
            r.arrow ? s ? n.arrow !== r.arrow && (o.removeChild(s),
            o.appendChild(V(r.arrow))) : o.appendChild(V(r.arrow)) : s && o.removeChild(s)
        }
        return r.className = "tippy-content",
        r.setAttribute("data-state", "hidden"),
        j(r, t.props),
        e.appendChild(n),
        n.appendChild(r),
        i(t.props, t.props),
        {
            popper: e,
            onUpdate: i
        }
    }
    S.$$tippy = !0;
    var B = 1
      , H = []
      , N = [];
    function U(e, n) {
        var a, c, m, h, b, C, A, O, L, k = M(e, Object.assign({}, D, {}, R((a = n,
        Object.keys(a).reduce((function(t, e) {
            return void 0 !== a[e] && (t[e] = a[e]),
            t
        }
        ), {}))))), P = !1, V = !1, j = !1, S = !1, U = [], _ = u(bt, k.interactiveDebounce), z = B++, F = (L = k.plugins).filter((function(t, e) {
            return L.indexOf(t) === e
        }
        )), W = {
            id: z,
            reference: e,
            popper: v(),
            popperInstance: null,
            props: k,
            state: {
                isEnabled: !0,
                isVisible: !1,
                isDestroyed: !1,
                isMounted: !1,
                isShown: !1
            },
            plugins: F,
            clearDelayTimeouts: function() {
                clearTimeout(c),
                clearTimeout(m),
                cancelAnimationFrame(h)
            },
            setProps: function(t) {
                if (W.state.isDestroyed)
                    return;
                it("onBeforeUpdate", [W, t]),
                gt();
                var n = W.props
                  , r = M(e, Object.assign({}, W.props, {}, t, {
                    ignoreAttributes: !0
                }));
                W.props = r,
                mt(),
                n.interactiveDebounce !== r.interactiveDebounce && (st(),
                _ = u(bt, r.interactiveDebounce));
                n.triggerTarget && !r.triggerTarget ? p(n.triggerTarget).forEach((function(t) {
                    t.removeAttribute("aria-expanded")
                }
                )) : r.triggerTarget && e.removeAttribute("aria-expanded");
                at(),
                rt(),
                q && q(n, r);
                W.popperInstance && (Et(),
                Ct().forEach((function(t) {
                    requestAnimationFrame(t._tippy.popperInstance.forceUpdate)
                }
                )));
                it("onAfterUpdate", [W, t])
            },
            setContent: function(t) {
                W.setProps({
                    content: t
                })
            },
            show: function() {
                var t = W.state.isVisible
                  , e = W.state.isDestroyed
                  , n = !W.state.isEnabled
                  , r = T.isTouch && !W.props.touch
                  , i = o(W.props.duration, 0, D.duration);
                if (t || e || n || r)
                    return;
                if (Z().hasAttribute("disabled"))
                    return;
                if (it("onShow", [W], !1),
                !1 === W.props.onShow(W))
                    return;
                W.state.isVisible = !0,
                Q() && (Y.style.visibility = "visible");
                rt(),
                ft(),
                W.state.isMounted || (Y.style.transition = "none");
                if (Q()) {
                    var a = et()
                      , u = a.box
                      , c = a.content;
                    y([u, c], 0)
                }
                A = function() {
                    if (W.state.isVisible && !S) {
                        if (S = !0,
                        Y.offsetHeight,
                        Y.style.transition = W.props.moveTransition,
                        Q() && W.props.animation) {
                            var t = et()
                              , e = t.box
                              , n = t.content;
                            y([e, n], i),
                            x([e, n], "visible")
                        }
                        ot(),
                        at(),
                        f(N, W),
                        W.state.isMounted = !0,
                        it("onMount", [W]),
                        W.props.animation && Q() && function(t, e) {
                            dt(t, e)
                        }(i, (function() {
                            W.state.isShown = !0,
                            it("onShown", [W])
                        }
                        ))
                    }
                }
                ,
                function() {
                    var t, e = W.props.appendTo, n = Z();
                    t = W.props.interactive && e === D.appendTo || "parent" === e ? n.parentNode : s(e, [n]);
                    t.contains(Y) || t.appendChild(Y);
                    Et()
                }()
            },
            hide: function() {
                var t = !W.state.isVisible
                  , e = W.state.isDestroyed
                  , n = !W.state.isEnabled
                  , r = o(W.props.duration, 1, D.duration);
                if (t || e || n)
                    return;
                if (it("onHide", [W], !1),
                !1 === W.props.onHide(W))
                    return;
                W.state.isVisible = !1,
                W.state.isShown = !1,
                S = !1,
                P = !1,
                Q() && (Y.style.visibility = "hidden");
                if (st(),
                lt(),
                rt(),
                Q()) {
                    var i = et()
                      , a = i.box
                      , s = i.content;
                    W.props.animation && (y([a, s], r),
                    x([a, s], "hidden"))
                }
                ot(),
                at(),
                W.props.animation ? Q() && function(t, e) {
                    dt(t, (function() {
                        !W.state.isVisible && Y.parentNode && Y.parentNode.contains(Y) && e()
                    }
                    ))
                }(r, W.unmount) : W.unmount()
            },
            hideWithInteractivity: function(t) {
                tt().addEventListener("mousemove", _),
                f(H, _),
                _(t)
            },
            enable: function() {
                W.state.isEnabled = !0
            },
            disable: function() {
                W.hide(),
                W.state.isEnabled = !1
            },
            unmount: function() {
                W.state.isVisible && W.hide();
                if (!W.state.isMounted)
                    return;
                Tt(),
                Ct().forEach((function(t) {
                    t._tippy.unmount()
                }
                )),
                Y.parentNode && Y.parentNode.removeChild(Y);
                N = N.filter((function(t) {
                    return t !== W
                }
                )),
                W.state.isMounted = !1,
                it("onHidden", [W])
            },
            destroy: function() {
                if (W.state.isDestroyed)
                    return;
                W.clearDelayTimeouts(),
                W.unmount(),
                gt(),
                delete e._tippy,
                W.state.isDestroyed = !0,
                it("onDestroy", [W])
            }
        };
        if (!k.render)
            return W;
        var X = k.render(W)
          , Y = X.popper
          , q = X.onUpdate;
        Y.setAttribute("data-tippy-root", ""),
        Y.id = "tippy-" + W.id,
        W.popper = Y,
        e._tippy = W,
        Y._tippy = W;
        var $ = F.map((function(t) {
            return t.fn(W)
        }
        ))
          , J = e.hasAttribute("aria-expanded");
        return mt(),
        at(),
        rt(),
        it("onCreate", [W]),
        k.showOnCreate && At(),
        Y.addEventListener("mouseenter", (function() {
            W.props.interactive && W.state.isVisible && W.clearDelayTimeouts()
        }
        )),
        Y.addEventListener("mouseleave", (function(t) {
            W.props.interactive && W.props.trigger.indexOf("mouseenter") >= 0 && (tt().addEventListener("mousemove", _),
            _(t))
        }
        )),
        W;
        function G() {
            var t = W.props.touch;
            return Array.isArray(t) ? t : [t, 0]
        }
        function K() {
            return "hold" === G()[0]
        }
        function Q() {
            var t;
            return !!(null == (t = W.props.render) ? void 0 : t.$$tippy)
        }
        function Z() {
            return O || e
        }
        function tt() {
            var t = Z().parentNode;
            return t ? w(t) : document
        }
        function et() {
            return I(Y)
        }
        function nt(t) {
            return W.state.isMounted && !W.state.isVisible || T.isTouch || b && "focus" === b.type ? 0 : o(W.props.delay, t ? 0 : 1, D.delay)
        }
        function rt() {
            Y.style.pointerEvents = W.props.interactive && W.state.isVisible ? "" : "none",
            Y.style.zIndex = "" + W.props.zIndex
        }
        function it(t, e, n) {
            var r;
            (void 0 === n && (n = !0),
            $.forEach((function(n) {
                n[t] && n[t].apply(void 0, e)
            }
            )),
            n) && (r = W.props)[t].apply(r, e)
        }
        function ot() {
            var t = W.props.aria;
            if (t.content) {
                var n = "aria-" + t.content
                  , r = Y.id;
                p(W.props.triggerTarget || e).forEach((function(t) {
                    var e = t.getAttribute(n);
                    if (W.state.isVisible)
                        t.setAttribute(n, e ? e + " " + r : r);
                    else {
                        var i = e && e.replace(r, "").trim();
                        i ? t.setAttribute(n, i) : t.removeAttribute(n)
                    }
                }
                ))
            }
        }
        function at() {
            !J && W.props.aria.expanded && p(W.props.triggerTarget || e).forEach((function(t) {
                W.props.interactive ? t.setAttribute("aria-expanded", W.state.isVisible && t === Z() ? "true" : "false") : t.removeAttribute("aria-expanded")
            }
            ))
        }
        function st() {
            tt().removeEventListener("mousemove", _),
            H = H.filter((function(t) {
                return t !== _
            }
            ))
        }
        function ut(t) {
            if (!(T.isTouch && (j || "mousedown" === t.type) || W.props.interactive && Y.contains(t.target))) {
                if (Z().contains(t.target)) {
                    if (T.isTouch)
                        return;
                    if (W.state.isVisible && W.props.trigger.indexOf("click") >= 0)
                        return
                } else
                    it("onClickOutside", [W, t]);
                !0 === W.props.hideOnClick && (W.clearDelayTimeouts(),
                W.hide(),
                V = !0,
                setTimeout((function() {
                    V = !1
                }
                )),
                W.state.isMounted || lt())
            }
        }
        function ct() {
            j = !0
        }
        function pt() {
            j = !1
        }
        function ft() {
            var t = tt();
            t.addEventListener("mousedown", ut, !0),
            t.addEventListener("touchend", ut, i),
            t.addEventListener("touchstart", pt, i),
            t.addEventListener("touchmove", ct, i)
        }
        function lt() {
            var t = tt();
            t.removeEventListener("mousedown", ut, !0),
            t.removeEventListener("touchend", ut, i),
            t.removeEventListener("touchstart", pt, i),
            t.removeEventListener("touchmove", ct, i)
        }
        function dt(t, e) {
            var n = et().box;
            function r(t) {
                t.target === n && (E(n, "remove", r),
                e())
            }
            if (0 === t)
                return e();
            E(n, "remove", C),
            E(n, "add", r),
            C = r
        }
        function vt(t, n, r) {
            void 0 === r && (r = !1),
            p(W.props.triggerTarget || e).forEach((function(e) {
                e.addEventListener(t, n, r),
                U.push({
                    node: e,
                    eventType: t,
                    handler: n,
                    options: r
                })
            }
            ))
        }
        function mt() {
            var t;
            K() && (vt("touchstart", ht, {
                passive: !0
            }),
            vt("touchend", yt, {
                passive: !0
            })),
            (t = W.props.trigger,
            t.split(/\s+/).filter(Boolean)).forEach((function(t) {
                if ("manual" !== t)
                    switch (vt(t, ht),
                    t) {
                    case "mouseenter":
                        vt("mouseleave", yt);
                        break;
                    case "focus":
                        vt(r ? "focusout" : "blur", xt);
                        break;
                    case "focusin":
                        vt("focusout", xt)
                    }
            }
            ))
        }
        function gt() {
            U.forEach((function(t) {
                var e = t.node
                  , n = t.eventType
                  , r = t.handler
                  , i = t.options;
                e.removeEventListener(n, r, i)
            }
            )),
            U = []
        }
        function ht(t) {
            var e, n = !1;
            if (W.state.isEnabled && !wt(t) && !V) {
                var r = "focus" === (null == (e = b) ? void 0 : e.type);
                b = t,
                O = t.currentTarget,
                at(),
                !W.state.isVisible && g(t) && H.forEach((function(e) {
                    return e(t)
                }
                )),
                "click" === t.type && (W.props.trigger.indexOf("mouseenter") < 0 || P) && !1 !== W.props.hideOnClick && W.state.isVisible ? n = !0 : At(t),
                "click" === t.type && (P = !n),
                n && !r && Ot(t)
            }
        }
        function bt(t) {
            var e = t.target
              , n = Z().contains(e) || Y.contains(e);
            "mousemove" === t.type && n || function(t, e) {
                var n = e.clientX
                  , r = e.clientY;
                return t.every((function(t) {
                    var e = t.popperRect
                      , i = t.popperState
                      , o = t.props.interactiveBorder
                      , a = l(i.placement)
                      , s = i.modifiersData.offset;
                    if (!s)
                        return !0;
                    var u = "bottom" === a ? s.top.y : 0
                      , c = "top" === a ? s.bottom.y : 0
                      , p = "right" === a ? s.left.x : 0
                      , f = "left" === a ? s.right.x : 0
                      , d = e.top - r + u > o
                      , v = r - e.bottom - c > o
                      , m = e.left - n + p > o
                      , g = n - e.right - f > o;
                    return d || v || m || g
                }
                ))
            }(Ct().concat(Y).map((function(t) {
                var e, n = null == (e = t._tippy.popperInstance) ? void 0 : e.state;
                return n ? {
                    popperRect: t.getBoundingClientRect(),
                    popperState: n,
                    props: k
                } : null
            }
            )).filter(Boolean), t) && (st(),
            Ot(t))
        }
        function yt(t) {
            wt(t) || W.props.trigger.indexOf("click") >= 0 && P || (W.props.interactive ? W.hideWithInteractivity(t) : Ot(t))
        }
        function xt(t) {
            W.props.trigger.indexOf("focusin") < 0 && t.target !== Z() || W.props.interactive && t.relatedTarget && Y.contains(t.relatedTarget) || Ot(t)
        }
        function wt(t) {
            return !!T.isTouch && K() !== t.type.indexOf("touch") >= 0
        }
        function Et() {
            Tt();
            var n = W.props
              , r = n.popperOptions
              , i = n.placement
              , o = n.offset
              , a = n.getReferenceClientRect
              , s = n.moveTransition
              , u = Q() ? I(Y).arrow : null
              , c = a ? {
                getBoundingClientRect: a,
                contextElement: a.contextElement || Z()
            } : e
              , p = [{
                name: "offset",
                options: {
                    offset: o
                }
            }, {
                name: "preventOverflow",
                options: {
                    padding: {
                        top: 2,
                        bottom: 2,
                        left: 5,
                        right: 5
                    }
                }
            }, {
                name: "flip",
                options: {
                    padding: 5
                }
            }, {
                name: "computeStyles",
                options: {
                    adaptive: !s
                }
            }, {
                name: "$$tippy",
                enabled: !0,
                phase: "beforeWrite",
                requires: ["computeStyles"],
                fn: function(t) {
                    var e = t.state;
                    if (Q()) {
                        var n = et().box;
                        ["placement", "reference-hidden", "escaped"].forEach((function(t) {
                            "placement" === t ? n.setAttribute("data-placement", e.placement) : e.attributes.popper["data-popper-" + t] ? n.setAttribute("data-" + t, "") : n.removeAttribute("data-" + t)
                        }
                        )),
                        e.attributes.popper = {}
                    }
                }
            }];
            Q() && u && p.push({
                name: "arrow",
                options: {
                    element: u,
                    padding: 3
                }
            }),
            p.push.apply(p, (null == r ? void 0 : r.modifiers) || []),
            W.popperInstance = t.createPopper(c, Y, Object.assign({}, r, {
                placement: i,
                onFirstUpdate: A,
                modifiers: p
            }))
        }
        function Tt() {
            W.popperInstance && (W.popperInstance.destroy(),
            W.popperInstance = null)
        }
        function Ct() {
            return d(Y.querySelectorAll("[data-tippy-root]"))
        }
        function At(t) {
            W.clearDelayTimeouts(),
            t && it("onTrigger", [W, t]),
            ft();
            var e = nt(!0)
              , n = G()
              , r = n[0]
              , i = n[1];
            T.isTouch && "hold" === r && i && (e = i),
            e ? c = setTimeout((function() {
                W.show()
            }
            ), e) : W.show()
        }
        function Ot(t) {
            if (W.clearDelayTimeouts(),
            it("onUntrigger", [W, t]),
            W.state.isVisible) {
                if (!(W.props.trigger.indexOf("mouseenter") >= 0 && W.props.trigger.indexOf("click") >= 0 && ["mouseleave", "mousemove"].indexOf(t.type) >= 0 && P)) {
                    var e = nt(!1);
                    e ? m = setTimeout((function() {
                        W.state.isVisible && W.hide()
                    }
                    ), e) : h = requestAnimationFrame((function() {
                        W.hide()
                    }
                    ))
                }
            } else
                lt()
        }
    }
    function _(t, e) {
        void 0 === e && (e = {});
        var n = D.plugins.concat(e.plugins || []);
        document.addEventListener("touchstart", A, i),
        window.addEventListener("blur", L);
        var r = Object.assign({}, e, {
            plugins: n
        })
          , o = b(t).reduce((function(t, e) {
            var n = e && U(e, r);
            return n && t.push(n),
            t
        }
        ), []);
        return m(t) ? o[0] : o
    }
    _.defaultProps = D,
    _.setDefaultProps = function(t) {
        Object.keys(t).forEach((function(e) {
            D[e] = t[e]
        }
        ))
    }
    ,
    _.currentInput = T;
    var z = {
        mouseover: "mouseenter",
        focusin: "focus",
        click: "click"
    };
    var F = {
        name: "animateFill",
        defaultValue: !1,
        fn: function(t) {
            var e;
            if (!(null == (e = t.props.render) ? void 0 : e.$$tippy))
                return {};
            var n = I(t.popper)
              , r = n.box
              , i = n.content
              , o = t.props.animateFill ? function() {
                var t = v();
                return t.className = "tippy-backdrop",
                x([t], "hidden"),
                t
            }() : null;
            return {
                onCreate: function() {
                    o && (r.insertBefore(o, r.firstElementChild),
                    r.setAttribute("data-animatefill", ""),
                    r.style.overflow = "hidden",
                    t.setProps({
                        arrow: !1,
                        animation: "shift-away"
                    }))
                },
                onMount: function() {
                    if (o) {
                        var t = r.style.transitionDuration
                          , e = Number(t.replace("ms", ""));
                        i.style.transitionDelay = Math.round(e / 10) + "ms",
                        o.style.transitionDuration = t,
                        x([o], "visible")
                    }
                },
                onShow: function() {
                    o && (o.style.transitionDuration = "0ms")
                },
                onHide: function() {
                    o && x([o], "hidden")
                }
            }
        }
    };
    var W = {
        clientX: 0,
        clientY: 0
    }
      , X = [];
    function Y(t) {
        var e = t.clientX
          , n = t.clientY;
        W = {
            clientX: e,
            clientY: n
        }
    }
    var q = {
        name: "followCursor",
        defaultValue: !1,
        fn: function(t) {
            var e = t.reference
              , n = w(t.props.triggerTarget || e)
              , r = !1
              , i = !1
              , o = !0
              , a = t.props;
            function s() {
                return "initial" === t.props.followCursor && t.state.isVisible
            }
            function u() {
                n.addEventListener("mousemove", f)
            }
            function c() {
                n.removeEventListener("mousemove", f)
            }
            function p() {
                r = !0,
                t.setProps({
                    getReferenceClientRect: null
                }),
                r = !1
            }
            function f(n) {
                var r = !n.target || e.contains(n.target)
                  , i = t.props.followCursor
                  , o = n.clientX
                  , a = n.clientY
                  , s = e.getBoundingClientRect()
                  , u = o - s.left
                  , c = a - s.top;
                !r && t.props.interactive || t.setProps({
                    getReferenceClientRect: function() {
                        var t = e.getBoundingClientRect()
                          , n = o
                          , r = a;
                        "initial" === i && (n = t.left + u,
                        r = t.top + c);
                        var s = "horizontal" === i ? t.top : r
                          , p = "vertical" === i ? t.right : n
                          , f = "horizontal" === i ? t.bottom : r
                          , l = "vertical" === i ? t.left : n;
                        return {
                            width: p - l,
                            height: f - s,
                            top: s,
                            right: p,
                            bottom: f,
                            left: l
                        }
                    }
                })
            }
            function l() {
                t.props.followCursor && (X.push({
                    instance: t,
                    doc: n
                }),
                function(t) {
                    t.addEventListener("mousemove", Y)
                }(n))
            }
            function d() {
                0 === (X = X.filter((function(e) {
                    return e.instance !== t
                }
                ))).filter((function(t) {
                    return t.doc === n
                }
                )).length && function(t) {
                    t.removeEventListener("mousemove", Y)
                }(n)
            }
            return {
                onCreate: l,
                onDestroy: d,
                onBeforeUpdate: function() {
                    a = t.props
                },
                onAfterUpdate: function(e, n) {
                    var o = n.followCursor;
                    r || void 0 !== o && a.followCursor !== o && (d(),
                    o ? (l(),
                    !t.state.isMounted || i || s() || u()) : (c(),
                    p()))
                },
                onMount: function() {
                    t.props.followCursor && !i && (o && (f(W),
                    o = !1),
                    s() || u())
                },
                onTrigger: function(t, e) {
                    g(e) && (W = {
                        clientX: e.clientX,
                        clientY: e.clientY
                    }),
                    i = "focus" === e.type
                },
                onHidden: function() {
                    t.props.followCursor && (p(),
                    c(),
                    o = !0)
                }
            }
        }
    };
    var $ = {
        name: "inlinePositioning",
        defaultValue: !1,
        fn: function(t) {
            var e, n = t.reference;
            var r = -1
              , i = !1
              , o = {
                name: "tippyInlinePositioning",
                enabled: !0,
                phase: "afterWrite",
                fn: function(i) {
                    var o = i.state;
                    t.props.inlinePositioning && (e !== o.placement && t.setProps({
                        getReferenceClientRect: function() {
                            return function(t) {
                                return function(t, e, n, r) {
                                    if (n.length < 2 || null === t)
                                        return e;
                                    if (2 === n.length && r >= 0 && n[0].left > n[1].right)
                                        return n[r] || e;
                                    switch (t) {
                                    case "top":
                                    case "bottom":
                                        var i = n[0]
                                          , o = n[n.length - 1]
                                          , a = "top" === t
                                          , s = i.top
                                          , u = o.bottom
                                          , c = a ? i.left : o.left
                                          , p = a ? i.right : o.right;
                                        return {
                                            top: s,
                                            bottom: u,
                                            left: c,
                                            right: p,
                                            width: p - c,
                                            height: u - s
                                        };
                                    case "left":
                                    case "right":
                                        var f = Math.min.apply(Math, n.map((function(t) {
                                            return t.left
                                        }
                                        )))
                                          , l = Math.max.apply(Math, n.map((function(t) {
                                            return t.right
                                        }
                                        )))
                                          , d = n.filter((function(e) {
                                            return "left" === t ? e.left === f : e.right === l
                                        }
                                        ))
                                          , v = d[0].top
                                          , m = d[d.length - 1].bottom;
                                        return {
                                            top: v,
                                            bottom: m,
                                            left: f,
                                            right: l,
                                            width: l - f,
                                            height: m - v
                                        };
                                    default:
                                        return e
                                    }
                                }(l(t), n.getBoundingClientRect(), d(n.getClientRects()), r)
                            }(o.placement)
                        }
                    }),
                    e = o.placement)
                }
            };
            function a() {
                var e;
                i || (e = function(t, e) {
                    var n;
                    return {
                        popperOptions: Object.assign({}, t.popperOptions, {
                            modifiers: [].concat(((null == (n = t.popperOptions) ? void 0 : n.modifiers) || []).filter((function(t) {
                                return t.name !== e.name
                            }
                            )), [e])
                        })
                    }
                }(t.props, o),
                i = !0,
                t.setProps(e),
                i = !1)
            }
            return {
                onCreate: a,
                onAfterUpdate: a,
                onTrigger: function(e, n) {
                    if (g(n)) {
                        var i = d(t.reference.getClientRects())
                          , o = i.find((function(t) {
                            return t.left - 2 <= n.clientX && t.right + 2 >= n.clientX && t.top - 2 <= n.clientY && t.bottom + 2 >= n.clientY
                        }
                        ));
                        r = i.indexOf(o)
                    }
                },
                onUntrigger: function() {
                    r = -1
                }
            }
        }
    };
    var J = {
        name: "sticky",
        defaultValue: !1,
        fn: function(t) {
            var e = t.reference
              , n = t.popper;
            function r(e) {
                return !0 === t.props.sticky || t.props.sticky === e
            }
            var i = null
              , o = null;
            function a() {
                var s = r("reference") ? (t.popperInstance ? t.popperInstance.state.elements.reference : e).getBoundingClientRect() : null
                  , u = r("popper") ? n.getBoundingClientRect() : null;
                (s && G(i, s) || u && G(o, u)) && t.popperInstance && t.popperInstance.update(),
                i = s,
                o = u,
                t.state.isMounted && requestAnimationFrame(a)
            }
            return {
                onMount: function() {
                    t.props.sticky && a()
                }
            }
        }
    };
    function G(t, e) {
        return !t || !e || (t.top !== e.top || t.right !== e.right || t.bottom !== e.bottom || t.left !== e.left)
    }
    return e && function(t) {
        var e = document.createElement("style");
        e.textContent = t,
        e.setAttribute("data-tippy-stylesheet", "");
        var n = document.head
          , r = document.querySelector("head>style,head>link");
        r ? n.insertBefore(e, r) : n.appendChild(e)
    }('.tippy-box[data-animation=fade][data-state=hidden]{opacity:0}[data-tippy-root]{max-width:calc(100vw - 10px)}.tippy-box{position:relative;background-color:#333;color:#fff;border-radius:4px;font-size:14px;line-height:1.4;outline:0;transition-property:transform,visibility,opacity}.tippy-box[data-placement^=top]>.tippy-arrow{bottom:0}.tippy-box[data-placement^=top]>.tippy-arrow:before{bottom:-7px;left:0;border-width:8px 8px 0;border-top-color:initial;transform-origin:center top}.tippy-box[data-placement^=bottom]>.tippy-arrow{top:0}.tippy-box[data-placement^=bottom]>.tippy-arrow:before{top:-7px;left:0;border-width:0 8px 8px;border-bottom-color:initial;transform-origin:center bottom}.tippy-box[data-placement^=left]>.tippy-arrow{right:0}.tippy-box[data-placement^=left]>.tippy-arrow:before{border-width:8px 0 8px 8px;border-left-color:initial;right:-7px;transform-origin:center left}.tippy-box[data-placement^=right]>.tippy-arrow{left:0}.tippy-box[data-placement^=right]>.tippy-arrow:before{left:-7px;border-width:8px 8px 8px 0;border-right-color:initial;transform-origin:center right}.tippy-box[data-inertia][data-state=visible]{transition-timing-function:cubic-bezier(.54,1.5,.38,1.11)}.tippy-arrow{width:16px;height:16px;color:#333}.tippy-arrow:before{content:"";position:absolute;border-color:transparent;border-style:solid}.tippy-content{position:relative;padding:5px 9px;z-index:1}'),
    _.setDefaultProps({
        plugins: [F, q, $, J],
        render: S
    }),
    _.createSingleton = function(t, e) {
        void 0 === e && (e = {});
        var n, r = t, i = [], o = e.overrides, a = [];
        function s() {
            i = r.map((function(t) {
                return t.reference
            }
            ))
        }
        function u(t) {
            r.forEach((function(e) {
                t ? e.enable() : e.disable()
            }
            ))
        }
        function p(t) {
            return r.map((function(e) {
                var r = e.setProps;
                return e.setProps = function(i) {
                    r(i),
                    e.reference === n && t.setProps(i)
                }
                ,
                function() {
                    e.setProps = r
                }
            }
            ))
        }
        u(!1),
        s();
        var f = {
            fn: function() {
                return {
                    onDestroy: function() {
                        u(!0)
                    },
                    onTrigger: function(t, e) {
                        var a = e.currentTarget
                          , s = i.indexOf(a);
                        if (a !== n) {
                            n = a;
                            var u = (o || []).concat("content").reduce((function(t, e) {
                                return t[e] = r[s].props[e],
                                t
                            }
                            ), {});
                            t.setProps(Object.assign({}, u, {
                                getReferenceClientRect: "function" == typeof u.getReferenceClientRect ? u.getReferenceClientRect : function() {
                                    return a.getBoundingClientRect()
                                }
                            }))
                        }
                    }
                }
            }
        }
          , l = _(v(), Object.assign({}, c(e, ["overrides"]), {
            plugins: [f].concat(e.plugins || []),
            triggerTarget: i
        }))
          , d = l.setProps;
        return l.setProps = function(t) {
            o = t.overrides || o,
            d(t)
        }
        ,
        l.setInstances = function(t) {
            u(!0),
            a.forEach((function(t) {
                return t()
            }
            )),
            r = t,
            u(!1),
            s(),
            p(l),
            l.setProps({
                triggerTarget: i
            })
        }
        ,
        a = p(l),
        l
    }
    ,
    _.delegate = function(t, e) {
        var n = []
          , r = []
          , i = !1
          , o = e.target
          , a = c(e, ["target"])
          , s = Object.assign({}, a, {
            trigger: "manual",
            touch: !1
        })
          , u = Object.assign({}, a, {
            showOnCreate: !0
        })
          , f = _(t, s);
        function l(t) {
            if (t.target && !i) {
                var n = t.target.closest(o);
                if (n) {
                    var a = n.getAttribute("data-tippy-trigger") || e.trigger || D.trigger;
                    if (!n._tippy && !("touchstart" === t.type && "boolean" == typeof u.touch || "touchstart" !== t.type && a.indexOf(z[t.type]) < 0)) {
                        var s = _(n, u);
                        s && (r = r.concat(s))
                    }
                }
            }
        }
        function d(t, e, r, i) {
            void 0 === i && (i = !1),
            t.addEventListener(e, r, i),
            n.push({
                node: t,
                eventType: e,
                handler: r,
                options: i
            })
        }
        return p(f).forEach((function(t) {
            var e = t.destroy
              , o = t.enable
              , a = t.disable;
            t.destroy = function(t) {
                void 0 === t && (t = !0),
                t && r.forEach((function(t) {
                    t.destroy()
                }
                )),
                r = [],
                n.forEach((function(t) {
                    var e = t.node
                      , n = t.eventType
                      , r = t.handler
                      , i = t.options;
                    e.removeEventListener(n, r, i)
                }
                )),
                n = [],
                e()
            }
            ,
            t.enable = function() {
                o(),
                r.forEach((function(t) {
                    return t.enable()
                }
                )),
                i = !1
            }
            ,
            t.disable = function() {
                a(),
                r.forEach((function(t) {
                    return t.disable()
                }
                )),
                i = !0
            }
            ,
            function(t) {
                var e = t.reference;
                d(e, "touchstart", l),
                d(e, "mouseover", l),
                d(e, "focusin", l),
                d(e, "click", l)
            }(t)
        }
        )),
        f
    }
    ,
    _.hideAll = function(t) {
        var e = void 0 === t ? {} : t
          , n = e.exclude
          , r = e.duration;
        N.forEach((function(t) {
            var e = !1;
            if (n && (e = h(n) ? t.reference === n : t.popper === n.popper),
            !e) {
                var i = t.props.duration;
                t.setProps({
                    duration: r
                }),
                t.hide(),
                t.state.isDestroyed || t.setProps({
                    duration: i
                })
            }
        }
        ))
    }
    ,
    _.roundArrow = '<svg width="16" height="6" xmlns="http://www.w3.org/2000/svg"><path d="M0 6s1.796-.013 4.67-3.615C5.851.9 6.93.006 8 0c1.07-.006 2.148.887 3.343 2.385C14.233 6.005 16 6 16 6H0z"></svg>',
    _
}
));
/*!
* jsoneditor.js
*
* @brief
* JSONEditor is a web-based tool to view, edit, format, and validate JSON.
* It has various modes such as a tree editor, a code editor, and a plain text
* editor.
*
* Supported browsers: Chrome, Firefox, Safari, Opera, Internet Explorer 8+
*
* @license
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy
* of the License at
*
* http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*
* Copyright (c) 2011-2020 Jos de Jong, http://jsoneditoronline.org
*
* @author Jos de Jong, <wjosdejong@gmail.com>
* @version 9.1.4
* @date 2020-12-07
*/
(function webpackUniversalModuleDefinition(root, factory) {
    if (typeof exports === 'object' && typeof module === 'object')
        module.exports = factory();
    else if (typeof define === 'function' && define.amd)
        define([], factory);
    else if (typeof exports === 'object')
        exports["JSONEditor"] = factory();
    else
        root["JSONEditor"] = factory();
}
)(window, function() {
    return (function(modules) {
        var installedModules = {};
        function __webpack_require__(moduleId) {
            if (installedModules[moduleId]) {
                return installedModules[moduleId].exports;
            }
            var module = installedModules[moduleId] = {
                i: moduleId,
                l: false,
                exports: {}
            };
            modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
            module.l = true;
            return module.exports;
        }
        __webpack_require__.m = modules;
        __webpack_require__.c = installedModules;
        __webpack_require__.d = function(exports, name, getter) {
            if (!__webpack_require__.o(exports, name)) {
                Object.defineProperty(exports, name, {
                    enumerable: true,
                    get: getter
                });
            }
        }
        ;
        __webpack_require__.r = function(exports) {
            if (typeof Symbol !== 'undefined' && Symbol.toStringTag) {
                Object.defineProperty(exports, Symbol.toStringTag, {
                    value: 'Module'
                });
            }
            Object.defineProperty(exports, '__esModule', {
                value: true
            });
        }
        ;
        __webpack_require__.t = function(value, mode) {
            if (mode & 1)
                value = __webpack_require__(value);
            if (mode & 8)
                return value;
            if ((mode & 4) && typeof value === 'object' && value && value.__esModule)
                return value;
            var ns = Object.create(null);
            __webpack_require__.r(ns);
            Object.defineProperty(ns, 'default', {
                enumerable: true,
                value: value
            });
            if (mode & 2 && typeof value != 'string')
                for (var key in value)
                    __webpack_require__.d(ns, key, function(key) {
                        return value[key];
                    }
                    .bind(null, key));
            return ns;
        }
        ;
        __webpack_require__.n = function(module) {
            var getter = module && module.__esModule ? function getDefault() {
                return module['default'];
            }
            : function getModuleExports() {
                return module;
            }
            ;
            __webpack_require__.d(getter, 'a', getter);
            return getter;
        }
        ;
        __webpack_require__.o = function(object, property) {
            return Object.prototype.hasOwnProperty.call(object, property);
        }
        ;
        __webpack_require__.p = "";
        return __webpack_require__(__webpack_require__.s = 23);
    }
    )([(function(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, "parse", function() {
            return parse;
        });
        __webpack_require__.d(__webpack_exports__, "trySimpleJsonRepair", function() {
            return trySimpleJsonRepair;
        });
        __webpack_require__.d(__webpack_exports__, "escapeUnicodeChars", function() {
            return escapeUnicodeChars;
        });
        __webpack_require__.d(__webpack_exports__, "validate", function() {
            return validate;
        });
        __webpack_require__.d(__webpack_exports__, "extend", function() {
            return extend;
        });
        __webpack_require__.d(__webpack_exports__, "clear", function() {
            return clear;
        });
        __webpack_require__.d(__webpack_exports__, "getType", function() {
            return getType;
        });
        __webpack_require__.d(__webpack_exports__, "isUrl", function() {
            return isUrl;
        });
        __webpack_require__.d(__webpack_exports__, "isArray", function() {
            return isArray;
        });
        __webpack_require__.d(__webpack_exports__, "getWindow", function() {
            return getWindow;
        });
        __webpack_require__.d(__webpack_exports__, "getAbsoluteLeft", function() {
            return getAbsoluteLeft;
        });
        __webpack_require__.d(__webpack_exports__, "getAbsoluteTop", function() {
            return getAbsoluteTop;
        });
        __webpack_require__.d(__webpack_exports__, "addClassName", function() {
            return addClassName;
        });
        __webpack_require__.d(__webpack_exports__, "removeAllClassNames", function() {
            return removeAllClassNames;
        });
        __webpack_require__.d(__webpack_exports__, "removeClassName", function() {
            return removeClassName;
        });
        __webpack_require__.d(__webpack_exports__, "stripFormatting", function() {
            return stripFormatting;
        });
        __webpack_require__.d(__webpack_exports__, "setEndOfContentEditable", function() {
            return setEndOfContentEditable;
        });
        __webpack_require__.d(__webpack_exports__, "selectContentEditable", function() {
            return selectContentEditable;
        });
        __webpack_require__.d(__webpack_exports__, "getSelection", function() {
            return getSelection;
        });
        __webpack_require__.d(__webpack_exports__, "setSelection", function() {
            return setSelection;
        });
        __webpack_require__.d(__webpack_exports__, "getSelectionOffset", function() {
            return getSelectionOffset;
        });
        __webpack_require__.d(__webpack_exports__, "setSelectionOffset", function() {
            return setSelectionOffset;
        });
        __webpack_require__.d(__webpack_exports__, "getInnerText", function() {
            return getInnerText;
        });
        __webpack_require__.d(__webpack_exports__, "hasParentNode", function() {
            return hasParentNode;
        });
        __webpack_require__.d(__webpack_exports__, "getInternetExplorerVersion", function() {
            return getInternetExplorerVersion;
        });
        __webpack_require__.d(__webpack_exports__, "isFirefox", function() {
            return isFirefox;
        });
        __webpack_require__.d(__webpack_exports__, "addEventListener", function() {
            return addEventListener;
        });
        __webpack_require__.d(__webpack_exports__, "removeEventListener", function() {
            return removeEventListener;
        });
        __webpack_require__.d(__webpack_exports__, "isChildOf", function() {
            return isChildOf;
        });
        __webpack_require__.d(__webpack_exports__, "parsePath", function() {
            return parsePath;
        });
        __webpack_require__.d(__webpack_exports__, "stringifyPath", function() {
            return stringifyPath;
        });
        __webpack_require__.d(__webpack_exports__, "improveSchemaError", function() {
            return improveSchemaError;
        });
        __webpack_require__.d(__webpack_exports__, "isPromise", function() {
            return isPromise;
        });
        __webpack_require__.d(__webpack_exports__, "isValidValidationError", function() {
            return isValidValidationError;
        });
        __webpack_require__.d(__webpack_exports__, "insideRect", function() {
            return insideRect;
        });
        __webpack_require__.d(__webpack_exports__, "debounce", function() {
            return debounce;
        });
        __webpack_require__.d(__webpack_exports__, "textDiff", function() {
            return textDiff;
        });
        __webpack_require__.d(__webpack_exports__, "getInputSelection", function() {
            return getInputSelection;
        });
        __webpack_require__.d(__webpack_exports__, "getIndexForPosition", function() {
            return getIndexForPosition;
        });
        __webpack_require__.d(__webpack_exports__, "getPositionForPath", function() {
            return getPositionForPath;
        });
        __webpack_require__.d(__webpack_exports__, "compileJSONPointer", function() {
            return compileJSONPointer;
        });
        __webpack_require__.d(__webpack_exports__, "getColorCSS", function() {
            return getColorCSS;
        });
        __webpack_require__.d(__webpack_exports__, "isValidColor", function() {
            return isValidColor;
        });
        __webpack_require__.d(__webpack_exports__, "makeFieldTooltip", function() {
            return makeFieldTooltip;
        });
        __webpack_require__.d(__webpack_exports__, "get", function() {
            return get;
        });
        __webpack_require__.d(__webpack_exports__, "findUniqueName", function() {
            return findUniqueName;
        });
        __webpack_require__.d(__webpack_exports__, "getChildPaths", function() {
            return getChildPaths;
        });
        __webpack_require__.d(__webpack_exports__, "sort", function() {
            return sort;
        });
        __webpack_require__.d(__webpack_exports__, "sortObjectKeys", function() {
            return sortObjectKeys;
        });
        __webpack_require__.d(__webpack_exports__, "parseString", function() {
            return parseString;
        });
        __webpack_require__.d(__webpack_exports__, "isTimestamp", function() {
            return isTimestamp;
        });
        __webpack_require__.d(__webpack_exports__, "formatSize", function() {
            return formatSize;
        });
        __webpack_require__.d(__webpack_exports__, "limitCharacters", function() {
            return limitCharacters;
        });
        __webpack_require__.d(__webpack_exports__, "isObject", function() {
            return isObject;
        });
        __webpack_require__.d(__webpack_exports__, "contains", function() {
            return contains;
        });
        __webpack_require__.d(__webpack_exports__, "isValidationErrorChanged", function() {
            return isValidationErrorChanged;
        });
        var _polyfills__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(19);
        var _polyfills__WEBPACK_IMPORTED_MODULE_0___default = __webpack_require__.n(_polyfills__WEBPACK_IMPORTED_MODULE_0__);
        var javascript_natural_sort__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(12);
        var javascript_natural_sort__WEBPACK_IMPORTED_MODULE_1___default = __webpack_require__.n(javascript_natural_sort__WEBPACK_IMPORTED_MODULE_1__);
        var simple_json_repair__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(7);
        var simple_json_repair__WEBPACK_IMPORTED_MODULE_2___default = __webpack_require__.n(simple_json_repair__WEBPACK_IMPORTED_MODULE_2__);
        var _assets_jsonlint_jsonlint__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(18);
        var _assets_jsonlint_jsonlint__WEBPACK_IMPORTED_MODULE_3___default = __webpack_require__.n(_assets_jsonlint_jsonlint__WEBPACK_IMPORTED_MODULE_3__);
        var json_source_map__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(20);
        var json_source_map__WEBPACK_IMPORTED_MODULE_4___default = __webpack_require__.n(json_source_map__WEBPACK_IMPORTED_MODULE_4__);
        var _i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(1);
        function _typeof(obj) {
            "@babel/helpers - typeof";
            if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
                _typeof = function _typeof(obj) {
                    return typeof obj;
                }
                ;
            } else {
                _typeof = function _typeof(obj) {
                    return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
                }
                ;
            }
            return _typeof(obj);
        }
        var MAX_ITEMS_FIELDS_COLLECTION = 10000;
        var YEAR_2000 = 946684800000;
        function parse(jsonString) {
            try {
                return JSON.parse(jsonString);
            } catch (err) {
                validate(jsonString);
                throw err;
            }
        }
        function trySimpleJsonRepair(jsonString) {
            try {
                return simple_json_repair__WEBPACK_IMPORTED_MODULE_2___default()(jsonString);
            } catch (err) {
                return jsonString;
            }
        }
        function escapeUnicodeChars(text) {
            return (text.replace(/[\u007F-\uFFFF]/g, function(c) {
                return "\\u" + ('0000' + c.charCodeAt(0).toString(16)).slice(-4);
            }));
        }
        function validate(jsonString) {
            if (typeof _assets_jsonlint_jsonlint__WEBPACK_IMPORTED_MODULE_3___default.a !== 'undefined') {
                _assets_jsonlint_jsonlint__WEBPACK_IMPORTED_MODULE_3___default.a.parse(jsonString);
            } else {
                JSON.parse(jsonString);
            }
        }
        function extend(a, b) {
            for (var prop in b) {
                if (hasOwnProperty(b, prop)) {
                    a[prop] = b[prop];
                }
            }
            return a;
        }
        function clear(a) {
            for (var prop in a) {
                if (hasOwnProperty(a, prop)) {
                    delete a[prop];
                }
            }
            return a;
        }
        function getType(object) {
            if (object === null) {
                return 'null';
            }
            if (object === undefined) {
                return 'undefined';
            }
            if (object instanceof Number || typeof object === 'number') {
                return 'number';
            }
            if (object instanceof String || typeof object === 'string') {
                return 'string';
            }
            if (object instanceof Boolean || typeof object === 'boolean') {
                return 'boolean';
            }
            if (object instanceof RegExp) {
                return 'regexp';
            }
            if (isArray(object)) {
                return 'array';
            }
            return 'object';
        }
        var isUrlRegex = /^https?:\/\/\S+$/;
        function isUrl(text) {
            return (typeof text === 'string' || text instanceof String) && isUrlRegex.test(text);
        }
        function isArray(obj) {
            return Object.prototype.toString.call(obj) === '[object Array]';
        }
        function getWindow(element) {
            return element.ownerDocument.defaultView;
        }
        function getAbsoluteLeft(elem) {
            var rect = elem.getBoundingClientRect();
            return rect.left + window.pageXOffset || document.scrollLeft || 0;
        }
        function getAbsoluteTop(elem) {
            var rect = elem.getBoundingClientRect();
            return rect.top + window.pageYOffset || document.scrollTop || 0;
        }
        function addClassName(elem, className) {
            var classes = elem.className.split(' ');
            if (classes.indexOf(className) === -1) {
                classes.push(className);
                elem.className = classes.join(' ');
            }
        }
        function removeAllClassNames(elem) {
            elem.className = '';
        }
        function removeClassName(elem, className) {
            var classes = elem.className.split(' ');
            var index = classes.indexOf(className);
            if (index !== -1) {
                classes.splice(index, 1);
                elem.className = classes.join(' ');
            }
        }
        function stripFormatting(divElement) {
            var childs = divElement.childNodes;
            for (var i = 0, iMax = childs.length; i < iMax; i++) {
                var child = childs[i];
                if (child.style) {
                    child.removeAttribute('style');
                }
                var attributes = child.attributes;
                if (attributes) {
                    for (var j = attributes.length - 1; j >= 0; j--) {
                        var attribute = attributes[j];
                        if (attribute.specified === true) {
                            child.removeAttribute(attribute.name);
                        }
                    }
                }
                stripFormatting(child);
            }
        }
        function setEndOfContentEditable(contentEditableElement) {
            var range, selection;
            if (document.createRange) {
                range = document.createRange();
                range.selectNodeContents(contentEditableElement);
                range.collapse(false);
                selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(range);
            }
        }
        function selectContentEditable(contentEditableElement) {
            if (!contentEditableElement || contentEditableElement.nodeName !== 'DIV') {
                return;
            }
            var sel, range;
            if (window.getSelection && document.createRange) {
                range = document.createRange();
                range.selectNodeContents(contentEditableElement);
                sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
            }
        }
        function getSelection() {
            if (window.getSelection) {
                var sel = window.getSelection();
                if (sel.getRangeAt && sel.rangeCount) {
                    return sel.getRangeAt(0);
                }
            }
            return null;
        }
        function setSelection(range) {
            if (range) {
                if (window.getSelection) {
                    var sel = window.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(range);
                }
            }
        }
        function getSelectionOffset() {
            var range = getSelection();
            if (range && 'startOffset'in range && 'endOffset'in range && range.startContainer && range.startContainer === range.endContainer) {
                return {
                    startOffset: range.startOffset,
                    endOffset: range.endOffset,
                    container: range.startContainer.parentNode
                };
            }
            return null;
        }
        function setSelectionOffset(params) {
            if (document.createRange && window.getSelection) {
                var selection = window.getSelection();
                if (selection) {
                    var range = document.createRange();
                    if (!params.container.firstChild) {
                        params.container.appendChild(document.createTextNode(''));
                    }
                    range.setStart(params.container.firstChild, params.startOffset);
                    range.setEnd(params.container.firstChild, params.endOffset);
                    setSelection(range);
                }
            }
        }
        function getInnerText(element, buffer) {
            var first = buffer === undefined;
            if (first) {
                buffer = {
                    _text: '',
                    flush: function flush() {
                        var text = this._text;
                        this._text = '';
                        return text;
                    },
                    set: function set(text) {
                        this._text = text;
                    }
                };
            }
            if (element.nodeValue) {
                var trimmedValue = element.nodeValue.replace(/\s*\n\s*/g, '');
                if (trimmedValue !== '') {
                    return buffer.flush() + trimmedValue;
                } else {
                    return '';
                }
            }
            if (element.hasChildNodes()) {
                var childNodes = element.childNodes;
                var innerText = '';
                for (var i = 0, iMax = childNodes.length; i < iMax; i++) {
                    var child = childNodes[i];
                    if (child.nodeName === 'DIV' || child.nodeName === 'P') {
                        var prevChild = childNodes[i - 1];
                        var prevName = prevChild ? prevChild.nodeName : undefined;
                        if (prevName && prevName !== 'DIV' && prevName !== 'P' && prevName !== 'BR') {
                            if (innerText !== '') {
                                innerText += '\n';
                            }
                            buffer.flush();
                        }
                        innerText += getInnerText(child, buffer);
                        buffer.set('\n');
                    } else if (child.nodeName === 'BR') {
                        innerText += buffer.flush();
                        buffer.set('\n');
                    } else {
                        innerText += getInnerText(child, buffer);
                    }
                }
                return innerText;
            }
            return '';
        }
        function hasParentNode(elem, parent) {
            var e = elem ? elem.parentNode : undefined;
            while (e) {
                if (e === parent) {
                    return true;
                }
                e = e.parentNode;
            }
            return false;
        }
        function getInternetExplorerVersion() {
            if (_ieVersion === -1) {
                var rv = -1;
                if (typeof navigator !== 'undefined' && navigator.appName === 'Microsoft Internet Explorer') {
                    var ua = navigator.userAgent;
                    var re = /MSIE ([0-9]+[.0-9]+)/;
                    if (re.exec(ua) != null) {
                        rv = parseFloat(RegExp.$1);
                    }
                }
                _ieVersion = rv;
            }
            return _ieVersion;
        }
        var _ieVersion = -1;
        function isFirefox() {
            return typeof navigator !== 'undefined' && navigator.userAgent.indexOf('Firefox') !== -1;
        }
        function addEventListener(element, action, listener, useCapture) {
            if (element.addEventListener) {
                if (useCapture === undefined) {
                    useCapture = false;
                }
                if (action === 'mousewheel' && isFirefox()) {
                    action = 'DOMMouseScroll';
                }
                element.addEventListener(action, listener, useCapture);
                return listener;
            } else if (element.attachEvent) {
                var f = function f() {
                    return listener.call(element, window.event);
                };
                element.attachEvent('on' + action, f);
                return f;
            }
        }
        function removeEventListener(element, action, listener, useCapture) {
            if (element.removeEventListener) {
                if (useCapture === undefined) {
                    useCapture = false;
                }
                if (action === 'mousewheel' && isFirefox()) {
                    action = 'DOMMouseScroll';
                }
                element.removeEventListener(action, listener, useCapture);
            } else if (element.detachEvent) {
                element.detachEvent('on' + action, listener);
            }
        }
        function isChildOf(elem, parent) {
            var e = elem.parentNode;
            while (e) {
                if (e === parent) {
                    return true;
                }
                e = e.parentNode;
            }
            return false;
        }
        function parsePath(jsonPath) {
            var path = [];
            var i = 0;
            function parseProperty() {
                var prop = '';
                while (jsonPath[i] !== undefined && /[\w$]/.test(jsonPath[i])) {
                    prop += jsonPath[i];
                    i++;
                }
                if (prop === '') {
                    throw new Error('Invalid JSON path: property name expected at index ' + i);
                }
                return prop;
            }
            function parseIndex(end) {
                var name = '';
                while (jsonPath[i] !== undefined && jsonPath[i] !== end) {
                    name += jsonPath[i];
                    i++;
                }
                if (jsonPath[i] !== end) {
                    throw new Error('Invalid JSON path: unexpected end, character ' + end + ' expected');
                }
                return name;
            }
            while (jsonPath[i] !== undefined) {
                if (jsonPath[i] === '.') {
                    i++;
                    path.push(parseProperty());
                } else if (jsonPath[i] === '[') {
                    i++;
                    if (jsonPath[i] === '\'' || jsonPath[i] === '"') {
                        var end = jsonPath[i];
                        i++;
                        path.push(parseIndex(end));
                        if (jsonPath[i] !== end) {
                            throw new Error('Invalid JSON path: closing quote \' expected at index ' + i);
                        }
                        i++;
                    } else {
                        var index = parseIndex(']').trim();
                        if (index.length === 0) {
                            throw new Error('Invalid JSON path: array value expected at index ' + i);
                        }
                        index = index === '*' ? index : JSON.parse(index);
                        path.push(index);
                    }
                    if (jsonPath[i] !== ']') {
                        throw new Error('Invalid JSON path: closing bracket ] expected at index ' + i);
                    }
                    i++;
                } else {
                    throw new Error('Invalid JSON path: unexpected character "' + jsonPath[i] + '" at index ' + i);
                }
            }
            return path;
        }
        function stringifyPath(path) {
            return path.map(function(p) {
                if (typeof p === 'number') {
                    return '[' + p + ']';
                } else if (typeof p === 'string' && p.match(/^[A-Za-z0-9_$]+$/)) {
                    return '.' + p;
                } else {
                    return '["' + p + '"]';
                }
            }).join('');
        }
        function improveSchemaError(error) {
            if (error.keyword === 'enum' && Array.isArray(error.schema)) {
                var enums = error.schema;
                if (enums) {
                    enums = enums.map(function(value) {
                        return JSON.stringify(value);
                    });
                    if (enums.length > 5) {
                        var more = ['(' + (enums.length - 5) + ' more...)'];
                        enums = enums.slice(0, 5);
                        enums.push(more);
                    }
                    error.message = 'should be equal to one of: ' + enums.join(', ');
                }
            }
            if (error.keyword === 'additionalProperties') {
                error.message = 'should NOT have additional property: ' + error.params.additionalProperty;
            }
            return error;
        }
        function isPromise(object) {
            return object && typeof object.then === 'function' && typeof object["catch"] === 'function';
        }
        function isValidValidationError(validationError) {
            return _typeof(validationError) === 'object' && Array.isArray(validationError.path) && typeof validationError.message === 'string';
        }
        function insideRect(parent, child, margin) {
            var _margin = margin !== undefined ? margin : 0;
            return child.left - _margin >= parent.left && child.right + _margin <= parent.right && child.top - _margin >= parent.top && child.bottom + _margin <= parent.bottom;
        }
        function debounce(func, wait, immediate) {
            var timeout;
            return function() {
                var context = this;
                var args = arguments;
                var later = function later() {
                    timeout = null;
                    if (!immediate)
                        func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow)
                    func.apply(context, args);
            }
            ;
        }
        function textDiff(oldText, newText) {
            var len = newText.length;
            var start = 0;
            var oldEnd = oldText.length;
            var newEnd = newText.length;
            while (newText.charAt(start) === oldText.charAt(start) && start < len) {
                start++;
            }
            while (newText.charAt(newEnd - 1) === oldText.charAt(oldEnd - 1) && newEnd > start && oldEnd > 0) {
                newEnd--;
                oldEnd--;
            }
            return {
                start: start,
                end: newEnd
            };
        }
        function getInputSelection(el) {
            var startIndex = 0;
            var endIndex = 0;
            var normalizedValue;
            var range;
            var textInputRange;
            var len;
            var endRange;
            if (typeof el.selectionStart === 'number' && typeof el.selectionEnd === 'number') {
                startIndex = el.selectionStart;
                endIndex = el.selectionEnd;
            } else {
                range = document.selection.createRange();
                if (range && range.parentElement() === el) {
                    len = el.value.length;
                    normalizedValue = el.value.replace(/\r\n/g, '\n');
                    textInputRange = el.createTextRange();
                    textInputRange.moveToBookmark(range.getBookmark());
                    endRange = el.createTextRange();
                    endRange.collapse(false);
                    if (textInputRange.compareEndPoints('StartToEnd', endRange) > -1) {
                        startIndex = endIndex = len;
                    } else {
                        startIndex = -textInputRange.moveStart('character', -len);
                        startIndex += normalizedValue.slice(0, startIndex).split('\n').length - 1;
                        if (textInputRange.compareEndPoints('EndToEnd', endRange) > -1) {
                            endIndex = len;
                        } else {
                            endIndex = -textInputRange.moveEnd('character', -len);
                            endIndex += normalizedValue.slice(0, endIndex).split('\n').length - 1;
                        }
                    }
                }
            }
            return {
                startIndex: startIndex,
                endIndex: endIndex,
                start: _positionForIndex(startIndex),
                end: _positionForIndex(endIndex)
            };
            function _positionForIndex(index) {
                var textTillIndex = el.value.substring(0, index);
                var row = (textTillIndex.match(/\n/g) || []).length + 1;
                var col = textTillIndex.length - textTillIndex.lastIndexOf('\n');
                return {
                    row: row,
                    column: col
                };
            }
        }
        function getIndexForPosition(el, row, column) {
            var text = el.value || '';
            if (row > 0 && column > 0) {
                var rows = text.split('\n', row);
                row = Math.min(rows.length, row);
                column = Math.min(rows[row - 1].length, column - 1);
                var columnCount = row === 1 ? column : column + 1;
                return rows.slice(0, row - 1).join('\n').length + columnCount;
            }
            return -1;
        }
        function getPositionForPath(text, paths) {
            var result = [];
            var jsmap;
            if (!paths || !paths.length) {
                return result;
            }
            try {
                jsmap = json_source_map__WEBPACK_IMPORTED_MODULE_4___default.a.parse(text);
            } catch (err) {
                return result;
            }
            paths.forEach(function(path) {
                var pathArr = parsePath(path);
                var pointerName = compileJSONPointer(pathArr);
                var pointer = jsmap.pointers[pointerName];
                if (pointer) {
                    result.push({
                        path: path,
                        line: pointer.key ? pointer.key.line : pointer.value ? pointer.value.line : 0,
                        column: pointer.key ? pointer.key.column : pointer.value ? pointer.value.column : 0
                    });
                }
            });
            return result;
        }
        function compileJSONPointer(path) {
            return path.map(function(p) {
                return '/' + String(p).replace(/~/g, '~0').replace(/\//g, '~1');
            }).join('');
        }
        function getColorCSS(color) {
            var ele = document.createElement('div');
            ele.style.color = color;
            return ele.style.color.split(/\s+/).join('').toLowerCase() || null;
        }
        function isValidColor(color) {
            return !!getColorCSS(color);
        }
        function makeFieldTooltip(schema, locale) {
            if (!schema) {
                return '';
            }
            var tooltip = '';
            if (schema.title) {
                tooltip += schema.title;
            }
            if (schema.description) {
                if (tooltip.length > 0) {
                    tooltip += '\n';
                }
                tooltip += schema.description;
            }
            if (schema["default"]) {
                if (tooltip.length > 0) {
                    tooltip += '\n\n';
                }
                tooltip += Object(_i18n__WEBPACK_IMPORTED_MODULE_5__["c"])('default', undefined, locale) + '\n';
                tooltip += JSON.stringify(schema["default"], null, 2);
            }
            if (Array.isArray(schema.examples) && schema.examples.length > 0) {
                if (tooltip.length > 0) {
                    tooltip += '\n\n';
                }
                tooltip += Object(_i18n__WEBPACK_IMPORTED_MODULE_5__["c"])('examples', undefined, locale) + '\n';
                schema.examples.forEach(function(example, index) {
                    tooltip += JSON.stringify(example, null, 2);
                    if (index !== schema.examples.length - 1) {
                        tooltip += '\n';
                    }
                });
            }
            return tooltip;
        }
        function get(object, path) {
            var value = object;
            for (var i = 0; i < path.length && value !== undefined && value !== null; i++) {
                value = value[path[i]];
            }
            return value;
        }
        function findUniqueName(name, existingPropNames) {
            var strippedName = name.replace(/ \(copy( \d+)?\)$/, '');
            var validName = strippedName;
            var i = 1;
            while (existingPropNames.indexOf(validName) !== -1) {
                var copy = 'copy' + (i > 1 ? ' ' + i : '');
                validName = strippedName + ' (' + copy + ')';
                i++;
            }
            return validName;
        }
        function getChildPaths(json, includeObjects) {
            var pathsMap = {};
            function getObjectChildPaths(json, pathsMap, rootPath, includeObjects) {
                var isValue = !Array.isArray(json) && !isObject(json);
                if (isValue || includeObjects) {
                    pathsMap[rootPath || ''] = true;
                }
                if (isObject(json)) {
                    Object.keys(json).forEach(function(field) {
                        getObjectChildPaths(json[field], pathsMap, rootPath + '.' + field, includeObjects);
                    });
                }
            }
            if (Array.isArray(json)) {
                var max = Math.min(json.length, MAX_ITEMS_FIELDS_COLLECTION);
                for (var i = 0; i < max; i++) {
                    var item = json[i];
                    getObjectChildPaths(item, pathsMap, '', includeObjects);
                }
            } else {
                pathsMap[''] = true;
            }
            return Object.keys(pathsMap).sort();
        }
        function sort(array, path, direction) {
            var parsedPath = path && path !== '.' ? parsePath(path) : [];
            var sign = direction === 'desc' ? -1 : 1;
            var sortedArray = array.slice();
            sortedArray.sort(function(a, b) {
                var aValue = get(a, parsedPath);
                var bValue = get(b, parsedPath);
                return sign * (aValue > bValue ? 1 : aValue < bValue ? -1 : 0);
            });
            return sortedArray;
        }
        function sortObjectKeys(object, direction) {
            var sign = direction === 'desc' ? -1 : 1;
            var sortedFields = Object.keys(object).sort(function(a, b) {
                return sign * javascript_natural_sort__WEBPACK_IMPORTED_MODULE_1___default()(a, b);
            });
            var sortedObject = {};
            sortedFields.forEach(function(field) {
                sortedObject[field] = object[field];
            });
            return sortedObject;
        }
        function parseString(str) {
            if (str === '') {
                return '';
            }
            var lower = str.toLowerCase();
            if (lower === 'null') {
                return null;
            }
            if (lower === 'true') {
                return true;
            }
            if (lower === 'false') {
                return false;
            }
            var num = Number(str);
            var numFloat = parseFloat(str);
            if (!isNaN(num) && !isNaN(numFloat)) {
                return num;
            }
            return str;
        }
        function isTimestamp(field, value) {
            return typeof value === 'number' && value > YEAR_2000 && isFinite(value) && Math.floor(value) === value && !isNaN(new Date(value).valueOf());
        }
        function formatSize(size) {
            if (size < 900) {
                return size.toFixed() + ' B';
            }
            var KB = size / 1000;
            if (KB < 900) {
                return KB.toFixed(1) + ' KB';
            }
            var MB = KB / 1000;
            if (MB < 900) {
                return MB.toFixed(1) + ' MB';
            }
            var GB = MB / 1000;
            if (GB < 900) {
                return GB.toFixed(1) + ' GB';
            }
            var TB = GB / 1000;
            return TB.toFixed(1) + ' TB';
        }
        function limitCharacters(text, maxCharacterCount) {
            if (text.length <= maxCharacterCount) {
                return text;
            }
            return text.slice(0, maxCharacterCount) + '...';
        }
        function isObject(value) {
            return _typeof(value) === 'object' && value !== null && !Array.isArray(value);
        }
        function contains(array, item) {
            return array.indexOf(item) !== -1;
        }
        function isValidationErrorChanged(currErr, prevErr) {
            if (!prevErr && !currErr) {
                return false;
            }
            if (prevErr && !currErr || !prevErr && currErr) {
                return true;
            }
            if (prevErr.length !== currErr.length) {
                return true;
            }
            var _loop = function _loop(i) {
                var pErr = void 0;
                if (currErr[i].type === 'error') {
                    pErr = prevErr.find(function(p) {
                        return p.line === currErr[i].line;
                    });
                } else {
                    pErr = prevErr.find(function(p) {
                        return p.dataPath === currErr[i].dataPath && p.schemaPath === currErr[i].schemaPath;
                    });
                }
                if (!pErr) {
                    return {
                        v: true
                    };
                }
            };
            for (var i = 0; i < currErr.length; ++i) {
                var _ret = _loop(i);
                if (_typeof(_ret) === "object")
                    return _ret.v;
            }
            return false;
        }
        function hasOwnProperty(object, key) {
            return Object.prototype.hasOwnProperty.call(object, key);
        }
    }
    ), (function(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        __webpack_require__.d(__webpack_exports__, "a", function() {
            return setLanguage;
        });
        __webpack_require__.d(__webpack_exports__, "b", function() {
            return setLanguages;
        });
        __webpack_require__.d(__webpack_exports__, "c", function() {
            return translate;
        });
        var _polyfills__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(19);
        var _polyfills__WEBPACK_IMPORTED_MODULE_0___default = __webpack_require__.n(_polyfills__WEBPACK_IMPORTED_MODULE_0__);
        var _locales = ['en', 'pt-BR', 'zh-CN', 'tr', 'ja', 'fr-FR', 'de'];
        var _defs = {
            en: {
                array: 'Array',
                auto: 'Auto',
                appendText: 'Append',
                appendTitle: 'Append a new field with type \'auto\' after this field (Ctrl+Shift+Ins)',
                appendSubmenuTitle: 'Select the type of the field to be appended',
                appendTitleAuto: 'Append a new field with type \'auto\' (Ctrl+Shift+Ins)',
                ascending: 'Ascending',
                ascendingTitle: 'Sort the childs of this ${type} in ascending order',
                actionsMenu: 'Click to open the actions menu (Ctrl+M)',
                cannotParseFieldError: 'Cannot parse field into JSON',
                cannotParseValueError: 'Cannot parse value into JSON',
                collapseAll: 'Collapse all fields',
                compactTitle: 'Compact JSON data, remove all whitespaces (Ctrl+Shift+\\)',
                descending: 'Descending',
                descendingTitle: 'Sort the childs of this ${type} in descending order',
                drag: 'Drag to move this field (Alt+Shift+Arrows)',
                duplicateKey: 'duplicate key',
                duplicateText: 'Duplicate',
                duplicateTitle: 'Duplicate selected fields (Ctrl+D)',
                duplicateField: 'Duplicate this field (Ctrl+D)',
                duplicateFieldError: 'Duplicate field name',
                empty: 'empty',
                expandAll: 'Expand all fields',
                expandTitle: 'Click to expand/collapse this field (Ctrl+E). \n' + 'Ctrl+Click to expand/collapse including all childs.',
                formatTitle: 'Format JSON data, with proper indentation and line feeds (Ctrl+\\)',
                insert: 'Insert',
                insertTitle: 'Insert a new field with type \'auto\' before this field (Ctrl+Ins)',
                insertSub: 'Select the type of the field to be inserted',
                object: 'Object',
                ok: 'Ok',
                redo: 'Redo (Ctrl+Shift+Z)',
                removeText: 'Remove',
                removeTitle: 'Remove selected fields (Ctrl+Del)',
                removeField: 'Remove this field (Ctrl+Del)',
                repairTitle: 'Repair JSON: fix quotes and escape characters, remove comments and JSONP notation, turn JavaScript objects into JSON.',
                searchTitle: 'Search fields and values',
                searchNextResultTitle: 'Next result (Enter)',
                searchPreviousResultTitle: 'Previous result (Shift + Enter)',
                selectNode: 'Select a node...',
                showAll: 'show all',
                showMore: 'show more',
                showMoreStatus: 'displaying ${visibleChilds} of ${totalChilds} items.',
                sort: 'Sort',
                sortTitle: 'Sort the childs of this ${type}',
                sortTitleShort: 'Sort contents',
                sortFieldLabel: 'Field:',
                sortDirectionLabel: 'Direction:',
                sortFieldTitle: 'Select the nested field by which to sort the array or object',
                sortAscending: 'Ascending',
                sortAscendingTitle: 'Sort the selected field in ascending order',
                sortDescending: 'Descending',
                sortDescendingTitle: 'Sort the selected field in descending order',
                string: 'String',
                transform: 'Transform',
                transformTitle: 'Filter, sort, or transform the childs of this ${type}',
                transformTitleShort: 'Filter, sort, or transform contents',
                extract: 'Extract',
                extractTitle: 'Extract this ${type}',
                transformQueryTitle: 'Enter a JMESPath query',
                transformWizardLabel: 'Wizard',
                transformWizardFilter: 'Filter',
                transformWizardSortBy: 'Sort by',
                transformWizardSelectFields: 'Select fields',
                transformQueryLabel: 'Query',
                transformPreviewLabel: 'Preview',
                type: 'Type',
                typeTitle: 'Change the type of this field',
                openUrl: 'Ctrl+Click or Ctrl+Enter to open url in new window',
                undo: 'Undo last action (Ctrl+Z)',
                validationCannotMove: 'Cannot move a field into a child of itself',
                autoType: 'Field type "auto". ' + 'The field type is automatically determined from the value ' + 'and can be a string, number, boolean, or null.',
                objectType: 'Field type "object". ' + 'An object contains an unordered set of key/value pairs.',
                arrayType: 'Field type "array". ' + 'An array contains an ordered collection of values.',
                stringType: 'Field type "string". ' + 'Field type is not determined from the value, ' + 'but always returned as string.',
                modeEditorTitle: 'Switch Editor Mode',
                modeCodeText: 'Code',
                modeCodeTitle: 'Switch to code highlighter',
                modeFormText: 'Form',
                modeFormTitle: 'Switch to form editor',
                modeTextText: 'Text',
                modeTextTitle: 'Switch to plain text editor',
                modeTreeText: 'Tree',
                modeTreeTitle: 'Switch to tree editor',
                modeViewText: 'View',
                modeViewTitle: 'Switch to tree view',
                modePreviewText: 'Preview',
                modePreviewTitle: 'Switch to preview mode',
                examples: 'Examples',
                "default": 'Default',
                containsInvalidProperties: 'Contains invalid properties',
                containsInvalidItems: 'Contains invalid items'
            },
            'zh-CN': {
                array: '数组',
                auto: '自动',
                appendText: '追加',
                appendTitle: '在此字段后追加一个类型为“auto”的新字段 (Ctrl+Shift+Ins)',
                appendSubmenuTitle: '选择要追加的字段类型',
                appendTitleAuto: '追加类型为“auto”的新字段 (Ctrl+Shift+Ins)',
                ascending: '升序',
                ascendingTitle: '升序排列${type}的子节点',
                actionsMenu: '点击打开动作菜单(Ctrl+M)',
                cannotParseFieldError: '无法将字段解析为JSON',
                cannotParseValueError: '无法将值解析为JSON',
                collapseAll: '缩进所有字段',
                compactTitle: '压缩JSON数据，删除所有空格 (Ctrl+Shift+\\)',
                descending: '降序',
                descendingTitle: '降序排列${type}的子节点',
                drag: '拖拽移动该节点(Alt+Shift+Arrows)',
                duplicateKey: '重复键',
                duplicateText: '复制',
                duplicateTitle: '复制选中字段(Ctrl+D)',
                duplicateField: '复制该字段(Ctrl+D)',
                duplicateFieldError: '重复的字段名称',
                empty: '清空',
                expandAll: '展开所有字段',
                expandTitle: '点击 展开/收缩 该字段(Ctrl+E). \n' + 'Ctrl+Click 展开/收缩 包含所有子节点.',
                formatTitle: '使用适当的缩进和换行符格式化JSON数据 (Ctrl+\\)',
                insert: '插入',
                insertTitle: '在此字段前插入类型为“auto”的新字段 (Ctrl+Ins)',
                insertSub: '选择要插入的字段类型',
                object: '对象',
                ok: 'Ok',
                redo: '重做 (Ctrl+Shift+Z)',
                removeText: '移除',
                removeTitle: '移除选中字段 (Ctrl+Del)',
                removeField: '移除该字段 (Ctrl+Del)',
                repairTitle: '修复JSON：修复引号和转义符，删除注释和JSONP表示法，将JavaScript对象转换为JSON。',
                selectNode: '选择一个节点...',
                showAll: '展示全部',
                showMore: '展示更多',
                showMoreStatus: '显示${totalChilds}的${visibleChilds}项目.',
                sort: '排序',
                sortTitle: '排序${type}的子节点',
                sortTitleShort: '内容排序',
                sortFieldLabel: '字段：',
                sortDirectionLabel: '方向：',
                sortFieldTitle: '选择用于对数组或对象排序的嵌套字段',
                sortAscending: '升序排序',
                sortAscendingTitle: '按照该字段升序排序',
                sortDescending: '降序排序',
                sortDescendingTitle: '按照该字段降序排序',
                string: '字符串',
                transform: '变换',
                transformTitle: '筛选，排序，或者转换${type}的子节点',
                transformTitleShort: '筛选，排序，或者转换内容',
                extract: '提取',
                extractTitle: '提取这个 ${type}',
                transformQueryTitle: '输入JMESPath查询',
                transformWizardLabel: '向导',
                transformWizardFilter: '筛选',
                transformWizardSortBy: '排序',
                transformWizardSelectFields: '选择字段',
                transformQueryLabel: '查询',
                transformPreviewLabel: '预览',
                type: '类型',
                typeTitle: '更改字段类型',
                openUrl: 'Ctrl+Click 或者 Ctrl+Enter 在新窗口打开链接',
                undo: '撤销上次动作 (Ctrl+Z)',
                validationCannotMove: '无法将字段移入其子节点',
                autoType: '字段类型 "auto". ' + '字段类型由值自动确定 ' + '可以为 string，number，boolean，或者 null.',
                objectType: '字段类型 "object". ' + '对象包含一组无序的键/值对.',
                arrayType: '字段类型 "array". ' + '数组包含值的有序集合.',
                stringType: '字段类型 "string". ' + '字段类型由值自动确定，' + '但始终作为字符串返回.',
                modeCodeText: '代码',
                modeCodeTitle: '切换至代码高亮',
                modeFormText: '表单',
                modeFormTitle: '切换至表单编辑',
                modeTextText: '文本',
                modeTextTitle: '切换至文本编辑',
                modeTreeText: '树',
                modeTreeTitle: '切换至树编辑',
                modeViewText: '视图',
                modeViewTitle: '切换至树视图',
                modePreviewText: '预览',
                modePreviewTitle: '切换至预览模式',
                examples: '例子',
                "default": '缺省',
                containsInvalidProperties: '包含无效的属性',
                containsInvalidItems: '包含无效项目'
            },
            'pt-BR': {
                array: 'Lista',
                auto: 'Automatico',
                appendText: 'Adicionar',
                appendTitle: 'Adicionar novo campo com tipo \'auto\' depois deste campo (Ctrl+Shift+Ins)',
                appendSubmenuTitle: 'Selecione o tipo do campo a ser adicionado',
                appendTitleAuto: 'Adicionar novo campo com tipo \'auto\' (Ctrl+Shift+Ins)',
                ascending: 'Ascendente',
                ascendingTitle: 'Organizar filhor do tipo ${type} em crescente',
                actionsMenu: 'Clique para abrir o menu de ações (Ctrl+M)',
                cannotParseFieldError: 'Não é possível analisar o campo no JSON',
                cannotParseValueError: 'Não é possível analisar o valor em JSON',
                collapseAll: 'Fechar todos campos',
                compactTitle: 'Dados JSON compactos, remova todos os espaços em branco (Ctrl+Shift+\\)',
                descending: 'Descendente',
                descendingTitle: 'Organizar o filhos do tipo ${type} em decrescente',
                duplicateKey: 'chave duplicada',
                drag: 'Arraste para mover este campo (Alt+Shift+Arrows)',
                duplicateText: 'Duplicar',
                duplicateTitle: 'Duplicar campos selecionados (Ctrl+D)',
                duplicateField: 'Duplicar este campo (Ctrl+D)',
                duplicateFieldError: 'Nome do campo duplicado',
                empty: 'vazio',
                expandAll: 'Expandir todos campos',
                expandTitle: 'Clique para expandir/encolher este campo (Ctrl+E). \n' + 'Ctrl+Click para expandir/encolher incluindo todos os filhos.',
                formatTitle: 'Formate dados JSON, com recuo e feeds de linha adequados (Ctrl+\\)',
                insert: 'Inserir',
                insertTitle: 'Inserir um novo campo do tipo \'auto\' antes deste campo (Ctrl+Ins)',
                insertSub: 'Selecionar o tipo de campo a ser inserido',
                object: 'Objeto',
                ok: 'Ok',
                redo: 'Refazer (Ctrl+Shift+Z)',
                removeText: 'Remover',
                removeTitle: 'Remover campos selecionados (Ctrl+Del)',
                removeField: 'Remover este campo (Ctrl+Del)',
                repairTitle: 'Repare JSON: corrija aspas e caracteres de escape, remova comentários e notação JSONP, transforme objetos JavaScript em JSON.',
                selectNode: 'Selecione um nódulo...',
                showAll: 'mostrar todos',
                showMore: 'mostrar mais',
                showMoreStatus: 'exibindo ${visibleChilds} de ${totalChilds} itens.',
                sort: 'Organizar',
                sortTitle: 'Organizar os filhos deste ${type}',
                sortTitleShort: 'Organizar os filhos',
                sortFieldLabel: 'Campo:',
                sortDirectionLabel: 'Direção:',
                sortFieldTitle: 'Selecione um campo filho pelo qual ordenar o array ou objeto',
                sortAscending: 'Ascendente',
                sortAscendingTitle: 'Ordenar o campo selecionado por ordem ascendente',
                sortDescending: 'Descendente',
                sortDescendingTitle: 'Ordenar o campo selecionado por ordem descendente',
                string: 'Texto',
                transform: 'Transformar',
                transformTitle: 'Filtrar, ordenar ou transformar os filhos deste ${type}',
                transformTitleShort: 'Filtrar, ordenar ou transformar conteúdos',
                transformQueryTitle: 'Insira uma expressão JMESPath',
                transformWizardLabel: 'Assistente',
                transformWizardFilter: 'Filtro',
                transformWizardSortBy: 'Ordenar por',
                transformWizardSelectFields: 'Selecionar campos',
                transformQueryLabel: 'Expressão',
                transformPreviewLabel: 'Visualizar',
                type: 'Tipo',
                typeTitle: 'Mudar o tipo deste campo',
                openUrl: 'Ctrl+Click ou Ctrl+Enter para abrir link em nova janela',
                undo: 'Desfazer último ação (Ctrl+Z)',
                validationCannotMove: 'Não pode mover um campo como filho dele mesmo',
                autoType: 'Campo do tipo "auto". ' + 'O tipo do campo é determinao automaticamente a partir do seu valor ' + 'e pode ser texto, número, verdade/falso ou nulo.',
                objectType: 'Campo do tipo "objeto". ' + 'Um objeto contém uma lista de pares com chave e valor.',
                arrayType: 'Campo do tipo "lista". ' + 'Uma lista contem uma coleção de valores ordenados.',
                stringType: 'Campo do tipo "string". ' + 'Campo do tipo nao é determinado através do seu valor, ' + 'mas sempre retornara um texto.',
                examples: 'Exemplos',
                "default": 'Revelia',
                containsInvalidProperties: 'Contém propriedades inválidas',
                containsInvalidItems: 'Contém itens inválidos'
            },
            tr: {
                array: 'Dizin',
                auto: 'Otomatik',
                appendText: 'Ekle',
                appendTitle: 'Bu alanın altına \'otomatik\' tipinde yeni bir alan ekle (Ctrl+Shift+Ins)',
                appendSubmenuTitle: 'Eklenecek alanın tipini seç',
                appendTitleAuto: '\'Otomatik\' tipinde yeni bir alan ekle (Ctrl+Shift+Ins)',
                ascending: 'Artan',
                ascendingTitle: '${type}\'ın alt tiplerini artan düzende sırala',
                actionsMenu: 'Aksiyon menüsünü açmak için tıklayın (Ctrl+M)',
                collapseAll: 'Tüm alanları kapat',
                descending: 'Azalan',
                descendingTitle: '${type}\'ın alt tiplerini azalan düzende sırala',
                drag: 'Bu alanı taşımak için sürükleyin (Alt+Shift+Arrows)',
                duplicateKey: 'Var olan anahtar',
                duplicateText: 'Aşağıya kopyala',
                duplicateTitle: 'Seçili alanlardan bir daha oluştur (Ctrl+D)',
                duplicateField: 'Bu alandan bir daha oluştur (Ctrl+D)',
                duplicateFieldError: 'Duplicate field name',
                cannotParseFieldError: 'Alan JSON\'a ayrıştırılamıyor',
                cannotParseValueError: 'JSON\'a değer ayrıştırılamıyor',
                empty: 'boş',
                expandAll: 'Tüm alanları aç',
                expandTitle: 'Bu alanı açmak/kapatmak için tıkla (Ctrl+E). \n' + 'Alt alanlarda dahil tüm alanları açmak için Ctrl+Click ',
                insert: 'Ekle',
                insertTitle: 'Bu alanın üstüne \'otomatik\' tipinde yeni bir alan ekle (Ctrl+Ins)',
                insertSub: 'Araya eklenecek alanın tipini seç',
                object: 'Nesne',
                ok: 'Tamam',
                redo: 'Yeniden yap (Ctrl+Shift+Z)',
                removeText: 'Kaldır',
                removeTitle: 'Seçilen alanları kaldır (Ctrl+Del)',
                removeField: 'Bu alanı kaldır (Ctrl+Del)',
                selectNode: 'Bir nesne seç...',
                showAll: 'tümünü göster',
                showMore: 'daha fazla göster',
                showMoreStatus: '${totalChilds} alanın ${visibleChilds} alt alanları gösteriliyor',
                sort: 'Sırala',
                sortTitle: '${type}\'ın alt alanlarını sırala',
                sortTitleShort: 'İçerikleri sırala',
                sortFieldLabel: 'Alan:',
                sortDirectionLabel: 'Yön:',
                sortFieldTitle: 'Diziyi veya nesneyi sıralamak için iç içe geçmiş alanı seçin',
                sortAscending: 'Artan',
                sortAscendingTitle: 'Seçili alanı artan düzende sırala',
                sortDescending: 'Azalan',
                sortDescendingTitle: 'Seçili alanı azalan düzende sırala',
                string: 'Karakter Dizisi',
                transform: 'Dönüştür',
                transformTitle: '${type}\'ın alt alanlarını filtrele, sırala veya dönüştür',
                transformTitleShort: 'İçerikleri filterele, sırala veya dönüştür',
                transformQueryTitle: 'JMESPath sorgusu gir',
                transformWizardLabel: 'Sihirbaz',
                transformWizardFilter: 'Filtre',
                transformWizardSortBy: 'Sırala',
                transformWizardSelectFields: 'Alanları seç',
                transformQueryLabel: 'Sorgu',
                transformPreviewLabel: 'Önizleme',
                type: 'Tip',
                typeTitle: 'Bu alanın tipini değiştir',
                openUrl: 'URL\'i yeni bir pencerede açmak için Ctrl+Click veya Ctrl+Enter',
                undo: 'Son değişikliği geri al (Ctrl+Z)',
                validationCannotMove: 'Alt alan olarak taşınamıyor',
                autoType: 'Alan tipi "otomatik". ' + 'Alan türü otomatik olarak değerden belirlenir' + 've bir dize, sayı, boolean veya null olabilir.',
                objectType: 'Alan tipi "nesne". ' + 'Bir nesne, sıralanmamış bir anahtar / değer çifti kümesi içerir.',
                arrayType: 'Alan tipi "dizi". ' + 'Bir dizi, düzenli değerler koleksiyonu içerir.',
                stringType: 'Alan tipi "karakter dizisi". ' + 'Alan türü değerden belirlenmez,' + 'ancak her zaman karakter dizisi olarak döndürülür.',
                modeCodeText: 'Kod',
                modeCodeTitle: 'Kod vurgulayıcıya geç',
                modeFormText: 'Form',
                modeFormTitle: 'Form düzenleyiciye geç',
                modeTextText: 'Metin',
                modeTextTitle: 'Düz metin düzenleyiciye geç',
                modeTreeText: 'Ağaç',
                modeTreeTitle: 'Ağaç düzenleyiciye geç',
                modeViewText: 'Görünüm',
                modeViewTitle: 'Ağaç görünümüne geç',
                examples: 'Örnekler',
                "default": 'Varsayılan',
                containsInvalidProperties: 'Geçersiz özellikler içeriyor',
                containsInvalidItems: 'Geçersiz öğeler içeriyor'
            },
            ja: {
                array: '配列',
                auto: 'オート',
                appendText: '追加',
                appendTitle: '次のフィールドに"オート"のフィールドを追加 (Ctrl+Shift+Ins)',
                appendSubmenuTitle: '追加するフィールドの型を選択してください',
                appendTitleAuto: '"オート"のフィールドを追加 (Ctrl+Shift+Ins)',
                ascending: '昇順',
                ascendingTitle: '${type}の子要素を昇順に並べ替え',
                actionsMenu: 'クリックしてアクションメニューを開く (Ctrl+M)',
                collapseAll: 'すべてを折りたたむ',
                descending: '降順',
                descendingTitle: '${type}の子要素を降順に並べ替え',
                drag: 'ドラッグして選択中のフィールドを移動 (Alt+Shift+Arrows)',
                duplicateKey: '複製キー',
                duplicateText: '複製',
                duplicateTitle: '選択中のフィールドを複製 (Ctrl+D)',
                duplicateField: '選択中のフィールドを複製 (Ctrl+D)',
                duplicateFieldError: 'フィールド名が重複しています',
                cannotParseFieldError: 'JSONのフィールドを解析できません',
                cannotParseValueError: 'JSONの値を解析できません',
                empty: '空',
                expandAll: 'すべてを展開',
                expandTitle: 'クリックしてフィールドを展開/折りたたむ (Ctrl+E). \n' + 'Ctrl+Click ですべての子要素を展開/折りたたむ',
                insert: '挿入',
                insertTitle: '選択中のフィールドの前に新しいフィールドを挿入 (Ctrl+Ins)',
                insertSub: '挿入するフィールドの型を選択',
                object: 'オブジェクト',
                ok: '実行',
                redo: 'やり直す (Ctrl+Shift+Z)',
                removeText: '削除',
                removeTitle: '選択中のフィールドを削除 (Ctrl+Del)',
                removeField: '選択中のフィールドを削除 (Ctrl+Del)',
                selectNode: 'ノードを選択...',
                showAll: 'すべてを表示',
                showMore: 'もっと見る',
                showMoreStatus: '${totalChilds}個のアイテムのうち ${visibleChilds}個を表示しています。',
                sort: '並べ替え',
                sortTitle: '${type}の子要素を並べ替え',
                sortTitleShort: '並べ替え',
                sortFieldLabel: 'フィールド:',
                sortDirectionLabel: '順序:',
                sortFieldTitle: '配列またはオブジェクトを並び替えるためのフィールドを選択',
                sortAscending: '昇順',
                sortAscendingTitle: '選択中のフィールドを昇順に並び替え',
                sortDescending: '降順',
                sortDescendingTitle: '選択中のフィールドを降順に並び替え',
                string: '文字列',
                transform: '変換',
                transformTitle: '${type}の子要素をフィルター・並び替え・変換する',
                transformTitleShort: '内容をフィルター・並び替え・変換する',
                extract: '抽出',
                extractTitle: '${type}を抽出',
                transformQueryTitle: 'JMESPathクエリを入力',
                transformWizardLabel: 'ウィザード',
                transformWizardFilter: 'フィルター',
                transformWizardSortBy: '並び替え',
                transformWizardSelectFields: 'フィールドを選択',
                transformQueryLabel: 'クエリ',
                transformPreviewLabel: 'プレビュー',
                type: '型',
                typeTitle: '選択中のフィールドの型を変更',
                openUrl: 'Ctrl+Click または Ctrl+Enter で 新規ウィンドウでURLを開く',
                undo: '元に戻す (Ctrl+Z)',
                validationCannotMove: '子要素に移動できません ',
                autoType: 'オート： ' + 'フィールドの型は値から自動的に決定されます。 ' + '(文字列・数値・ブール・null)',
                objectType: 'オブジェクト： ' + 'オブジェクトは順序が決まっていないキーと値のペア組み合わせです。',
                arrayType: '配列： ' + '配列は順序が決まっている値の集合体です。',
                stringType: '文字列： ' + 'フィールド型は値から決定されませんが、' + '常に文字列として返されます。',
                modeCodeText: 'コードモード',
                modeCodeTitle: 'ハイライトモードに切り替え',
                modeFormText: 'フォームモード',
                modeFormTitle: 'フォームモードに切り替え',
                modeTextText: 'テキストモード',
                modeTextTitle: 'テキストモードに切り替え',
                modeTreeText: 'ツリーモード',
                modeTreeTitle: 'ツリーモードに切り替え',
                modeViewText: 'ビューモード',
                modeViewTitle: 'ビューモードに切り替え',
                modePreviewText: 'プレビュー',
                modePreviewTitle: 'プレビューに切り替え',
                examples: '例',
                "default": 'デフォルト',
                containsInvalidProperties: '無効なプロパティが含まれています',
                containsInvalidItems: '無効なアイテムが含まれています'
            },
            'fr-FR': {
                array: 'Liste',
                auto: 'Auto',
                appendText: 'Ajouter',
                appendTitle: 'Ajouter un champ de type \'auto\' après ce champ (Ctrl+Shift+Ins)',
                appendSubmenuTitle: 'Sélectionner le type du champ à ajouter',
                appendTitleAuto: 'Ajouter un champ de type \'auto\' (Ctrl+Shift+Ins)',
                ascending: 'Ascendant',
                ascendingTitle: 'Trier les enfants de ce ${type} par ordre ascendant',
                actionsMenu: 'Ouvrir le menu des actions (Ctrl+M)',
                collapseAll: 'Regrouper',
                descending: 'Descendant',
                descendingTitle: 'Trier les enfants de ce ${type} par ordre descendant',
                drag: 'Déplacer (Alt+Shift+Arrows)',
                duplicateKey: 'Dupliquer la clé',
                duplicateText: 'Dupliquer',
                duplicateTitle: 'Dupliquer les champs sélectionnés (Ctrl+D)',
                duplicateField: 'Dupliquer ce champ (Ctrl+D)',
                duplicateFieldError: 'Dupliquer le nom de champ',
                cannotParseFieldError: 'Champ impossible à parser en JSON',
                cannotParseValueError: 'Valeur impossible à parser en JSON',
                empty: 'vide',
                expandAll: 'Étendre',
                expandTitle: 'Étendre/regrouper ce champ (Ctrl+E). \n' + 'Ctrl+Click pour étendre/regrouper avec tous les champs.',
                insert: 'Insérer',
                insertTitle: 'Insérer un champ de type \'auto\' avant ce champ (Ctrl+Ins)',
                insertSub: 'Sélectionner le type de champ à insérer',
                object: 'Objet',
                ok: 'Ok',
                redo: 'Rejouer (Ctrl+Shift+Z)',
                removeText: 'Supprimer',
                removeTitle: 'Supprimer les champs sélectionnés (Ctrl+Del)',
                removeField: 'Supprimer ce champ (Ctrl+Del)',
                searchTitle: 'Rechercher champs et valeurs',
                searchNextResultTitle: 'Résultat suivant (Enter)',
                searchPreviousResultTitle: 'Résultat précédent (Shift + Enter)',
                selectNode: 'Sélectionner un nœud...',
                showAll: 'voir tout',
                showMore: 'voir plus',
                showMoreStatus: '${visibleChilds} éléments affichés de ${totalChilds}.',
                sort: 'Trier',
                sortTitle: 'Trier les champs de ce ${type}',
                sortTitleShort: 'Trier',
                sortFieldLabel: 'Champ:',
                sortDirectionLabel: 'Direction:',
                sortFieldTitle: 'Sélectionner les champs permettant de trier les listes et objet',
                sortAscending: 'Ascendant',
                sortAscendingTitle: 'Trier les champs sélectionnés par ordre ascendant',
                sortDescending: 'Descendant',
                sortDescendingTitle: 'Trier les champs sélectionnés par ordre descendant',
                string: 'Chaîne',
                transform: 'Transformer',
                transformTitle: 'Filtrer, trier, or transformer les enfants de ce ${type}',
                transformTitleShort: 'Filtrer, trier ou transformer le contenu',
                extract: 'Extraire',
                extractTitle: 'Extraire ce ${type}',
                transformQueryTitle: 'Saisir une requête JMESPath',
                transformWizardLabel: 'Assistant',
                transformWizardFilter: 'Filtrer',
                transformWizardSortBy: 'Trier par',
                transformWizardSelectFields: 'Sélectionner les champs',
                transformQueryLabel: 'Requête',
                transformPreviewLabel: 'Prévisualisation',
                type: 'Type',
                typeTitle: 'Changer le type de ce champ',
                openUrl: 'Ctrl+Click ou Ctrl+Enter pour ouvrir l\'url dans une autre fenêtre',
                undo: 'Annuler la dernière action (Ctrl+Z)',
                validationCannotMove: 'Cannot move a field into a child of itself',
                autoType: 'Champe de type "auto". ' + 'Ce type de champ est automatiquement déterminé en fonction de la valeur ' + 'et peut être de type "chaîne", "nombre", "booléen" ou null.',
                objectType: 'Champ de type "objet". ' + 'Un objet contient un ensemble non ordonné de paires clé/valeur.',
                arrayType: 'Champ de type "liste". ' + 'Une liste contient une collection ordonnée de valeurs.',
                stringType: 'Champ de type "chaîne". ' + 'Ce type de champ n\'est pas déterminé en fonction de la valeur, ' + 'mais retourne systématiquement une chaîne de caractères.',
                modeEditorTitle: 'Changer mode d\'édition',
                modeCodeText: 'Code',
                modeCodeTitle: 'Activer surlignage code',
                modeFormText: 'Formulaire',
                modeFormTitle: 'Activer formulaire',
                modeTextText: 'Texte',
                modeTextTitle: 'Activer éditeur texte',
                modeTreeText: 'Arbre',
                modeTreeTitle: 'Activer éditeur arbre',
                modeViewText: 'Lecture seule',
                modeViewTitle: 'Activer vue arbre',
                modePreviewText: 'Prévisualisation',
                modePreviewTitle: 'Activer mode prévisualiser',
                examples: 'Exemples',
                "default": 'Défaut',
                containsInvalidProperties: 'Contient des propriétés non valides',
                containsInvalidItems: 'Contient des éléments invalides'
            },
            de: {
                array: 'Auflistung',
                auto: 'Auto',
                appendText: 'anhängen',
                appendTitle: 'Fügen Sie nach diesem Feld ein neues Feld mit dem Typ \'auto\' ein (Strg+Umschalt+Ein)',
                appendSubmenuTitle: 'Wählen Sie den Typ des neuen Feldes',
                appendTitleAuto: 'Ein neues Feld vom Typ \'auto\' hinzufügen (Strg+Umschalt+Ein)',
                ascending: 'Aufsteigend',
                ascendingTitle: 'Sortieren Sie die Elemente dieses ${type} in aufsteigender Reihenfolge',
                actionsMenu: 'Klicken Sie zum Öffnen des Aktionsmenüs (Strg+M)',
                cannotParseFieldError: 'Feld kann nicht in JSON geparst werden',
                cannotParseValueError: 'Wert kann nicht in JSON geparst werden',
                collapseAll: 'Alle Felder zuklappen',
                compactTitle: 'JSON-Daten verdichten, alle Leerzeichen entfernen (Strg+Umschalt+\\)',
                descending: 'Absteigend',
                descendingTitle: 'Sortieren Sie die Elemente dieses ${type} in absteigender Reihenfolge',
                drag: 'Ziehen, um dieses Feld zu verschieben (Alt+Umschalt+Pfeile)',
                duplicateKey: 'Doppelter Schlüssel',
                duplicateText: 'Duplikat',
                duplicateTitle: 'Ausgewählte Felder duplizieren (Strg+D)',
                duplicateField: 'Dieses Feld duplizieren (Strg+D)',
                duplicateFieldError: 'Doppelter Feldname',
                empty: 'leer',
                expandAll: 'Alle Felder anzeigen',
                expandTitle: 'Klicken Sie, um dieses Feld zu erweitern/zu kollabieren (Strg+E). \nStrg+Klicken Sie, um dieses Feld einschließlich aller Elemente zu erweitern/zu kollabieren.',
                formatTitle: 'JSON-Daten mit korrekter Einrückung und Zeilenvorschüben formatieren (Strg+\\)',
                insert: 'einfügen',
                insertTitle: 'Fügen Sie vor diesem Feld ein neues Feld mit dem Typ \'auto\' ein (Strg+Einfg)',
                insertSub: 'Wählen Sie den Typ des neuen Feldes',
                object: 'Objekt',
                ok: 'Ok',
                redo: 'Wiederholen (Strg+Umschalt+Z)',
                removeText: 'entfernen',
                removeTitle: 'Ausgewählte Felder entfernen (Strg+Entf)',
                removeField: 'Dieses Feld entfernen (Strg+Entf)',
                repairTitle: 'JSON reparieren: Anführungszeichen und Escape-Zeichen korrigieren, Kommentare und JSONP-Notation entfernen, JavaScript-Objekte in JSON umwandeln.',
                searchTitle: 'Suchfelder und Werte',
                searchNextResultTitle: 'Nächstes Ergebnis (Enter)',
                searchPreviousResultTitle: 'Vorheriges Ergebnis (Umschalt + Eingabe)',
                selectNode: 'Wählen Sie einen Knoten aus...',
                showAll: 'alle anzeigen',
                showMore: 'mehr anzeigen',
                showMoreStatus: 'Anzeige von ${visibleChilds} von ${totalChilds}-Elementen.',
                sort: 'Sortieren',
                sortTitle: 'Sortieren Sie die Elemente dieses ${type}',
                sortTitleShort: 'Inhalt sortieren',
                sortFieldLabel: 'Feld:',
                sortDirectionLabel: 'Richtung:',
                sortFieldTitle: 'Wählen Sie das verschachtelte Feld, nach dem das Array oder Objekt sortiert werden soll.',
                sortAscending: 'Aufsteigend',
                sortAscendingTitle: 'Sortieren Sie das ausgewählte Feld in aufsteigender Reihenfolge',
                sortDescending: 'Absteigend',
                sortDescendingTitle: 'Sortieren Sie das ausgewählte Feld in absteigender Reihenfolge',
                string: 'Zeichenfolge',
                transform: 'Verwandeln',
                transformTitle: 'Die Elemente dieses ${type} filtern, sortieren oder transformieren',
                transformTitleShort: 'Inhalte filtern, sortieren oder transformieren',
                extract: 'Auszug',
                extractTitle: 'Extrahieren Sie diesen ${type}',
                transformQueryTitle: 'Eine JMESPath-Abfrage eingeben',
                transformWizardLabel: 'Zauberer',
                transformWizardFilter: 'Filter',
                transformWizardSortBy: 'Sortieren nach',
                transformWizardSelectFields: 'Felder auswählen',
                transformQueryLabel: 'Anfrage',
                transformPreviewLabel: 'Vorschau',
                type: 'Geben Sie  ein.',
                typeTitle: 'Ändern Sie den Typ dieses Feldes',
                openUrl: 'Strg+Klicken oder Strg+Eingabe, um die URL in einem neuen Fenster zu öffnen',
                undo: 'Letzte Aktion rückgängig machen (Strg+Z)',
                validationCannotMove: 'Kann ein Feld nicht in ein Kind seiner selbst verschieben',
                autoType: 'Feldtyp "auto". Der Feldtyp wird automatisch aus dem Wert bestimmt und kann ein String, eine Zahl, boolesch oder null sein.',
                objectType: 'Feldtyp "Objekt". Ein Objekt enthält eine ungeordnete Menge von Schlüssel/Wert-Paaren.',
                arrayType: 'Feldtyp "Array". Ein Array enthält eine geordnete Sammlung von Werten.',
                stringType: 'Feldtyp "Zeichenfolge". Der Feldtyp wird nicht aus dem Wert bestimmt, sondern immer als Zeichenfolge zurückgegeben.',
                modeEditorTitle: 'Editor-Modus umschalten',
                modeCodeText: 'Code',
                modeCodeTitle: 'Umschalten auf Code-Highlighter',
                modeFormText: 'Formular',
                modeFormTitle: 'Zum Formular-Editor wechseln',
                modeTextText: 'Text',
                modeTextTitle: 'Zum Editor für einfachen Text wechseln',
                modeTreeText: 'Baum',
                modeTreeTitle: 'Zum Baum-Editor wechseln',
                modeViewText: 'Siehe',
                modeViewTitle: 'Zur Baumansicht wechseln',
                modePreviewText: 'Vorschau',
                modePreviewTitle: 'In den Vorschau-Modus wechseln',
                examples: 'Beispiele',
                "default": 'Standardmäßig',
                containsInvalidProperties: 'Enthält ungültige Eigenschaften',
                containsInvalidItems: 'Enthält ungültige Elemente'
            }
        };
        var _defaultLang = 'en';
        var userLang = typeof navigator !== 'undefined' ? navigator.language || navigator.userLanguage : undefined;
        var _lang = _locales.find(function(l) {
            return l === userLang;
        }) || _defaultLang;
        function setLanguage(lang) {
            if (!lang) {
                return;
            }
            var langFound = _locales.find(function(l) {
                return l === lang;
            });
            if (langFound) {
                _lang = langFound;
            } else {
                console.error('Language not found');
            }
        }
        function setLanguages(languages) {
            if (!languages) {
                return;
            }
            var _loop = function _loop(language) {
                var langFound = _locales.find(function(l) {
                    return l === language;
                });
                if (!langFound) {
                    _locales.push(language);
                }
                _defs[language] = Object.assign({}, _defs[_defaultLang], _defs[language], languages[language]);
            };
            for (var language in languages) {
                _loop(language);
            }
        }
        function translate(key, data, lang) {
            if (!lang) {
                lang = _lang;
            }
            var text = _defs[lang][key] || _defs[_defaultLang][key] || key;
            if (data) {
                for (var dataKey in data) {
                    text = text.replace('${' + dataKey + '}', data[dataKey]);
                }
            }
            return text;
        }
    }
    ), (function(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        __webpack_require__.d(__webpack_exports__, "a", function() {
            return DEFAULT_MODAL_ANCHOR;
        });
        __webpack_require__.d(__webpack_exports__, "d", function() {
            return SIZE_LARGE;
        });
        __webpack_require__.d(__webpack_exports__, "b", function() {
            return MAX_PREVIEW_CHARACTERS;
        });
        __webpack_require__.d(__webpack_exports__, "c", function() {
            return PREVIEW_HISTORY_LIMIT;
        });
        var DEFAULT_MODAL_ANCHOR = document.body;
        var SIZE_LARGE = 10 * 1024 * 1024;
        var MAX_PREVIEW_CHARACTERS = 20000;
        var PREVIEW_HISTORY_LIMIT = 2 * 1024 * 1024 * 1024;
    }
    ), (function(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        __webpack_require__.d(__webpack_exports__, "a", function() {
            return ContextMenu;
        });
        var _createAbsoluteAnchor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(11);
        var _util__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(0);
        var _i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(1);
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value"in descriptor)
                    descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps)
                _defineProperties(Constructor.prototype, protoProps);
            if (staticProps)
                _defineProperties(Constructor, staticProps);
            return Constructor;
        }
        var ContextMenu = function() {
            function ContextMenu(items, options) {
                _classCallCheck(this, ContextMenu);
                this.dom = {};
                var me = this;
                var dom = this.dom;
                this.anchor = undefined;
                this.items = items;
                this.eventListeners = {};
                this.selection = undefined;
                this.onClose = options ? options.close : undefined;
                this.limitHeight = options ? options.limitHeight : false;
                var root = document.createElement('div');
                root.className = 'jsoneditor-contextmenu-root';
                dom.root = root;
                var menu = document.createElement('div');
                menu.className = 'jsoneditor-contextmenu';
                dom.menu = menu;
                root.appendChild(menu);
                var list = document.createElement('ul');
                list.className = 'jsoneditor-menu';
                menu.appendChild(list);
                dom.list = list;
                dom.items = [];
                var focusButton = document.createElement('button');
                focusButton.type = 'button';
                dom.focusButton = focusButton;
                var li = document.createElement('li');
                li.style.overflow = 'hidden';
                li.style.height = '0';
                li.appendChild(focusButton);
                list.appendChild(li);
                function createMenuItems(list, domItems, items) {
                    items.forEach(function(item) {
                        if (item.type === 'separator') {
                            var separator = document.createElement('div');
                            separator.className = 'jsoneditor-separator';
                            var _li = document.createElement('li');
                            _li.appendChild(separator);
                            list.appendChild(_li);
                        } else {
                            var domItem = {};
                            var _li2 = document.createElement('li');
                            list.appendChild(_li2);
                            var button = document.createElement('button');
                            button.type = 'button';
                            button.className = item.className;
                            domItem.button = button;
                            if (item.title) {
                                button.title = item.title;
                            }
                            if (item.click) {
                                button.onclick = function(event) {
                                    event.preventDefault();
                                    me.hide();
                                    item.click();
                                }
                                ;
                            }
                            _li2.appendChild(button);
                            if (item.submenu) {
                                var divIcon = document.createElement('div');
                                divIcon.className = 'jsoneditor-icon';
                                button.appendChild(divIcon);
                                var divText = document.createElement('div');
                                divText.className = 'jsoneditor-text' + (item.click ? '' : ' jsoneditor-right-margin');
                                divText.appendChild(document.createTextNode(item.text));
                                button.appendChild(divText);
                                var buttonSubmenu;
                                if (item.click) {
                                    button.className += ' jsoneditor-default';
                                    var buttonExpand = document.createElement('button');
                                    buttonExpand.type = 'button';
                                    domItem.buttonExpand = buttonExpand;
                                    buttonExpand.className = 'jsoneditor-expand';
                                    var buttonExpandInner = document.createElement('div');
                                    buttonExpandInner.className = 'jsoneditor-expand';
                                    buttonExpand.appendChild(buttonExpandInner);
                                    _li2.appendChild(buttonExpand);
                                    if (item.submenuTitle) {
                                        buttonExpand.title = item.submenuTitle;
                                    }
                                    buttonSubmenu = buttonExpand;
                                } else {
                                    var divExpand = document.createElement('div');
                                    divExpand.className = 'jsoneditor-expand';
                                    button.appendChild(divExpand);
                                    buttonSubmenu = button;
                                }
                                buttonSubmenu.onclick = function(event) {
                                    event.preventDefault();
                                    me._onExpandItem(domItem);
                                    buttonSubmenu.focus();
                                }
                                ;
                                var domSubItems = [];
                                domItem.subItems = domSubItems;
                                var ul = document.createElement('ul');
                                domItem.ul = ul;
                                ul.className = 'jsoneditor-menu';
                                ul.style.height = '0';
                                _li2.appendChild(ul);
                                createMenuItems(ul, domSubItems, item.submenu);
                            } else {
                                var icon = document.createElement('div');
                                icon.className = 'jsoneditor-icon';
                                button.appendChild(icon);
                                var text = document.createElement('div');
                                text.className = 'jsoneditor-text';
                                text.appendChild(document.createTextNode(Object(_i18n__WEBPACK_IMPORTED_MODULE_2__["c"])(item.text)));
                                button.appendChild(text);
                            }
                            domItems.push(domItem);
                        }
                    });
                }
                createMenuItems(list, this.dom.items, items);
                this.maxHeight = 0;
                items.forEach(function(item) {
                    var height = (items.length + (item.submenu ? item.submenu.length : 0)) * 24;
                    me.maxHeight = Math.max(me.maxHeight, height);
                });
            }
            _createClass(ContextMenu, [{
                key: "_getVisibleButtons",
                value: function _getVisibleButtons() {
                    var buttons = [];
                    var me = this;
                    this.dom.items.forEach(function(item) {
                        buttons.push(item.button);
                        if (item.buttonExpand) {
                            buttons.push(item.buttonExpand);
                        }
                        if (item.subItems && item === me.expandedItem) {
                            item.subItems.forEach(function(subItem) {
                                buttons.push(subItem.button);
                                if (subItem.buttonExpand) {
                                    buttons.push(subItem.buttonExpand);
                                }
                            });
                        }
                    });
                    return buttons;
                }
            }, {
                key: "show",
                value: function show(anchor, frame, ignoreParent) {
                    this.hide();
                    var showBelow = true;
                    var parent = anchor.parentNode;
                    var anchorRect = anchor.getBoundingClientRect();
                    var parentRect = parent.getBoundingClientRect();
                    var frameRect = frame.getBoundingClientRect();
                    var me = this;
                    this.dom.absoluteAnchor = Object(_createAbsoluteAnchor__WEBPACK_IMPORTED_MODULE_0__["a"])(anchor, frame, function() {
                        me.hide();
                    });
                    if (anchorRect.bottom + this.maxHeight < frameRect.bottom) {} else if (anchorRect.top - this.maxHeight > frameRect.top) {
                        showBelow = false;
                    } else {}
                    var topGap = ignoreParent ? 0 : anchorRect.top - parentRect.top;
                    if (showBelow) {
                        var anchorHeight = anchor.offsetHeight;
                        this.dom.menu.style.left = '0';
                        this.dom.menu.style.top = topGap + anchorHeight + 'px';
                        this.dom.menu.style.bottom = '';
                    } else {
                        this.dom.menu.style.left = '0';
                        this.dom.menu.style.top = '';
                        this.dom.menu.style.bottom = '0px';
                    }
                    if (this.limitHeight) {
                        var margin = 10;
                        var maxPossibleMenuHeight = showBelow ? frameRect.bottom - anchorRect.bottom - margin : anchorRect.top - frameRect.top - margin;
                        this.dom.list.style.maxHeight = maxPossibleMenuHeight + 'px';
                        this.dom.list.style.overflowY = 'auto';
                    }
                    this.dom.absoluteAnchor.appendChild(this.dom.root);
                    this.selection = Object(_util__WEBPACK_IMPORTED_MODULE_1__["getSelection"])();
                    this.anchor = anchor;
                    setTimeout(function() {
                        me.dom.focusButton.focus();
                    }, 0);
                    if (ContextMenu.visibleMenu) {
                        ContextMenu.visibleMenu.hide();
                    }
                    ContextMenu.visibleMenu = this;
                }
            }, {
                key: "hide",
                value: function hide() {
                    if (this.dom.absoluteAnchor) {
                        this.dom.absoluteAnchor.destroy();
                        delete this.dom.absoluteAnchor;
                    }
                    if (this.dom.root.parentNode) {
                        this.dom.root.parentNode.removeChild(this.dom.root);
                        if (this.onClose) {
                            this.onClose();
                        }
                    }
                    if (ContextMenu.visibleMenu === this) {
                        ContextMenu.visibleMenu = undefined;
                    }
                }
            }, {
                key: "_onExpandItem",
                value: function _onExpandItem(domItem) {
                    var me = this;
                    var alreadyVisible = domItem === this.expandedItem;
                    var expandedItem = this.expandedItem;
                    if (expandedItem) {
                        expandedItem.ul.style.height = '0';
                        expandedItem.ul.style.padding = '';
                        setTimeout(function() {
                            if (me.expandedItem !== expandedItem) {
                                expandedItem.ul.style.display = '';
                                Object(_util__WEBPACK_IMPORTED_MODULE_1__["removeClassName"])(expandedItem.ul.parentNode, 'jsoneditor-selected');
                            }
                        }, 300);
                        this.expandedItem = undefined;
                    }
                    if (!alreadyVisible) {
                        var ul = domItem.ul;
                        ul.style.display = 'block';
                        ul.clientHeight;
                        setTimeout(function() {
                            if (me.expandedItem === domItem) {
                                var childsHeight = 0;
                                for (var i = 0; i < ul.childNodes.length; i++) {
                                    childsHeight += ul.childNodes[i].clientHeight;
                                }
                                ul.style.height = childsHeight + 'px';
                                ul.style.padding = '5px 10px';
                            }
                        }, 0);
                        Object(_util__WEBPACK_IMPORTED_MODULE_1__["addClassName"])(ul.parentNode, 'jsoneditor-selected');
                        this.expandedItem = domItem;
                    }
                }
            }, {
                key: "_onKeyDown",
                value: function _onKeyDown(event) {
                    var target = event.target;
                    var keynum = event.which;
                    var handled = false;
                    var buttons, targetIndex, prevButton, nextButton;
                    if (keynum === 27) {
                        if (this.selection) {
                            Object(_util__WEBPACK_IMPORTED_MODULE_1__["setSelection"])(this.selection);
                        }
                        if (this.anchor) {
                            this.anchor.focus();
                        }
                        this.hide();
                        handled = true;
                    } else if (keynum === 9) {
                        if (!event.shiftKey) {
                            buttons = this._getVisibleButtons();
                            targetIndex = buttons.indexOf(target);
                            if (targetIndex === buttons.length - 1) {
                                buttons[0].focus();
                                handled = true;
                            }
                        } else {
                            buttons = this._getVisibleButtons();
                            targetIndex = buttons.indexOf(target);
                            if (targetIndex === 0) {
                                buttons[buttons.length - 1].focus();
                                handled = true;
                            }
                        }
                    } else if (keynum === 37) {
                        if (target.className === 'jsoneditor-expand') {
                            buttons = this._getVisibleButtons();
                            targetIndex = buttons.indexOf(target);
                            prevButton = buttons[targetIndex - 1];
                            if (prevButton) {
                                prevButton.focus();
                            }
                        }
                        handled = true;
                    } else if (keynum === 38) {
                        buttons = this._getVisibleButtons();
                        targetIndex = buttons.indexOf(target);
                        prevButton = buttons[targetIndex - 1];
                        if (prevButton && prevButton.className === 'jsoneditor-expand') {
                            prevButton = buttons[targetIndex - 2];
                        }
                        if (!prevButton) {
                            prevButton = buttons[buttons.length - 1];
                        }
                        if (prevButton) {
                            prevButton.focus();
                        }
                        handled = true;
                    } else if (keynum === 39) {
                        buttons = this._getVisibleButtons();
                        targetIndex = buttons.indexOf(target);
                        nextButton = buttons[targetIndex + 1];
                        if (nextButton && nextButton.className === 'jsoneditor-expand') {
                            nextButton.focus();
                        }
                        handled = true;
                    } else if (keynum === 40) {
                        buttons = this._getVisibleButtons();
                        targetIndex = buttons.indexOf(target);
                        nextButton = buttons[targetIndex + 1];
                        if (nextButton && nextButton.className === 'jsoneditor-expand') {
                            nextButton = buttons[targetIndex + 2];
                        }
                        if (!nextButton) {
                            nextButton = buttons[0];
                        }
                        if (nextButton) {
                            nextButton.focus();
                            handled = true;
                        }
                        handled = true;
                    }
                    if (handled) {
                        event.stopPropagation();
                        event.preventDefault();
                    }
                }
            }]);
            return ContextMenu;
        }();
        ContextMenu.visibleMenu = undefined;
    }
    ), (function(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        __webpack_require__.d(__webpack_exports__, "a", function() {
            return createQuery;
        });
        __webpack_require__.d(__webpack_exports__, "b", function() {
            return executeQuery;
        });
        var jmespath__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(21);
        var jmespath__WEBPACK_IMPORTED_MODULE_0___default = __webpack_require__.n(jmespath__WEBPACK_IMPORTED_MODULE_0__);
        var _util__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(0);
        function createQuery(json, queryOptions) {
            var sort = queryOptions.sort
              , filter = queryOptions.filter
              , projection = queryOptions.projection;
            var query = '';
            if (filter) {
                var examplePath = filter.field !== '@' ? ['0'].concat(Object(_util__WEBPACK_IMPORTED_MODULE_1__["parsePath"])('.' + filter.field)) : ['0'];
                var exampleValue = Object(_util__WEBPACK_IMPORTED_MODULE_1__["get"])(json, examplePath);
                var value1 = typeof exampleValue === 'string' ? filter.value : Object(_util__WEBPACK_IMPORTED_MODULE_1__["parseString"])(filter.value);
                query += '[? ' + filter.field + ' ' + filter.relation + ' ' + '`' + JSON.stringify(value1) + '`' + ']';
            } else {
                query += Array.isArray(json) ? '[*]' : '@';
            }
            if (sort) {
                if (sort.direction === 'desc') {
                    query += ' | reverse(sort_by(@, &' + sort.field + '))';
                } else {
                    query += ' | sort_by(@, &' + sort.field + ')';
                }
            }
            if (projection) {
                if (query[query.length - 1] !== ']') {
                    query += ' | [*]';
                }
                if (projection.fields.length === 1) {
                    query += '.' + projection.fields[0];
                } else if (projection.fields.length > 1) {
                    query += '.{' + projection.fields.map(function(value) {
                        var parts = value.split('.');
                        var last = parts[parts.length - 1];
                        return last + ': ' + value;
                    }).join(', ') + '}';
                } else {}
            }
            return query;
        }
        function executeQuery(json, query) {
            return jmespath__WEBPACK_IMPORTED_MODULE_0___default.a.search(json, query);
        }
    }
    ), (function(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, "showSortModal", function() {
            return showSortModal;
        });
        var picomodal__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(14);
        var picomodal__WEBPACK_IMPORTED_MODULE_0___default = __webpack_require__.n(picomodal__WEBPACK_IMPORTED_MODULE_0__);
        var _i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(1);
        var _util__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(0);
        function showSortModal(container, json, onSort, options) {
            var paths = Array.isArray(json) ? Object(_util__WEBPACK_IMPORTED_MODULE_2__["getChildPaths"])(json) : [''];
            var selectedPath = options && options.path && Object(_util__WEBPACK_IMPORTED_MODULE_2__["contains"])(paths, options.path) ? options.path : paths[0];
            var selectedDirection = options && options.direction || 'asc';
            var content = '<div class="pico-modal-contents">' + '<div class="pico-modal-header">' + Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('sort') + '</div>' + '<form>' + '<table>' + '<tbody>' + '<tr>' + '  <td>' + Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('sortFieldLabel') + ' </td>' + '  <td class="jsoneditor-modal-input">' + '  <div class="jsoneditor-select-wrapper">' + '    <select id="field" title="' + Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('sortFieldTitle') + '">' + '    </select>' + '  </div>' + '  </td>' + '</tr>' + '<tr>' + '  <td>' + Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('sortDirectionLabel') + ' </td>' + '  <td class="jsoneditor-modal-input">' + '  <div id="direction" class="jsoneditor-button-group">' + '<input type="button" ' + 'value="' + Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('sortAscending') + '" ' + 'title="' + Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('sortAscendingTitle') + '" ' + 'data-value="asc" ' + 'class="jsoneditor-button-first jsoneditor-button-asc"/>' + '<input type="button" ' + 'value="' + Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('sortDescending') + '" ' + 'title="' + Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('sortDescendingTitle') + '" ' + 'data-value="desc" ' + 'class="jsoneditor-button-last jsoneditor-button-desc"/>' + '  </div>' + '  </td>' + '</tr>' + '<tr>' + '<td colspan="2" class="jsoneditor-modal-input jsoneditor-modal-actions">' + '  <input type="submit" id="ok" value="' + Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('ok') + '" />' + '</td>' + '</tr>' + '</tbody>' + '</table>' + '</form>' + '</div>';
            picomodal__WEBPACK_IMPORTED_MODULE_0___default()({
                parent: container,
                content: content,
                overlayClass: 'jsoneditor-modal-overlay',
                overlayStyles: {
                    backgroundColor: 'rgb(1,1,1)',
                    opacity: 0.3
                },
                modalClass: 'jsoneditor-modal jsoneditor-modal-sort'
            }).afterCreate(function(modal) {
                var form = modal.modalElem().querySelector('form');
                var ok = modal.modalElem().querySelector('#ok');
                var field = modal.modalElem().querySelector('#field');
                var direction = modal.modalElem().querySelector('#direction');
                function preprocessPath(path) {
                    return path === '' ? '@' : path[0] === '.' ? path.slice(1) : path;
                }
                paths.forEach(function(path) {
                    var option = document.createElement('option');
                    option.text = preprocessPath(path);
                    option.value = path;
                    field.appendChild(option);
                });
                function setDirection(value) {
                    direction.value = value;
                    direction.className = 'jsoneditor-button-group jsoneditor-button-group-value-' + direction.value;
                }
                field.value = selectedPath || paths[0];
                setDirection(selectedDirection || 'asc');
                direction.onclick = function(event) {
                    setDirection(event.target.getAttribute('data-value'));
                }
                ;
                ok.onclick = function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    modal.close();
                    onSort({
                        path: field.value,
                        direction: direction.value
                    });
                }
                ;
                if (form) {
                    form.onsubmit = ok.onclick;
                }
            }).afterClose(function(modal) {
                modal.destroy();
            }).show();
        }
    }
    ), (function(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, "showTransformModal", function() {
            return showTransformModal;
        });
        var picoModal = __webpack_require__(14);
        var picoModal_default = __webpack_require__.n(picoModal);
        var selectr = __webpack_require__(10);
        var selectr_default = __webpack_require__.n(selectr);
        var i18n = __webpack_require__(1);
        function _typeof(obj) {
            "@babel/helpers - typeof";
            if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
                _typeof = function _typeof(obj) {
                    return typeof obj;
                }
                ;
            } else {
                _typeof = function _typeof(obj) {
                    return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
                }
                ;
            }
            return _typeof(obj);
        }
        function stringifyPartial(value, space, limit) {
            var _space;
            if (typeof space === 'number') {
                if (space > 10) {
                    _space = repeat(' ', 10);
                } else if (space >= 1) {
                    _space = repeat(' ', space);
                }
            } else if (typeof space === 'string' && space !== '') {
                _space = space;
            }
            var output = stringifyValue(value, _space, '', limit);
            return output.length > limit ? slice(output, limit) + '...' : output;
        }
        function stringifyValue(value, space, indent, limit) {
            if (typeof value === 'boolean' || value instanceof Boolean || value === null || typeof value === 'number' || value instanceof Number || typeof value === 'string' || value instanceof String || value instanceof Date) {
                return JSON.stringify(value);
            }
            if (Array.isArray(value)) {
                return stringifyArray(value, space, indent, limit);
            }
            if (value && _typeof(value) === 'object') {
                return stringifyObject(value, space, indent, limit);
            }
            return undefined;
        }
        function stringifyArray(array, space, indent, limit) {
            var childIndent = space ? indent + space : undefined;
            var str = space ? '[\n' : '[';
            for (var i = 0; i < array.length; i++) {
                var item = array[i];
                if (space) {
                    str += childIndent;
                }
                if (typeof item !== 'undefined' && typeof item !== 'function') {
                    str += stringifyValue(item, space, childIndent, limit);
                } else {
                    str += 'null';
                }
                if (i < array.length - 1) {
                    str += space ? ',\n' : ',';
                }
                if (str.length > limit) {
                    return str + '...';
                }
            }
            str += space ? '\n' + indent + ']' : ']';
            return str;
        }
        function stringifyObject(object, space, indent, limit) {
            var childIndent = space ? indent + space : undefined;
            var first = true;
            var str = space ? '{\n' : '{';
            if (typeof object.toJSON === 'function') {
                return stringifyValue(object.toJSON(), space, indent, limit);
            }
            for (var key in object) {
                if (jsonUtils_hasOwnProperty(object, key)) {
                    var value = object[key];
                    if (first) {
                        first = false;
                    } else {
                        str += space ? ',\n' : ',';
                    }
                    str += space ? childIndent + '"' + key + '": ' : '"' + key + '":';
                    str += stringifyValue(value, space, childIndent, limit);
                    if (str.length > limit) {
                        return str + '...';
                    }
                }
            }
            str += space ? '\n' + indent + '}' : '}';
            return str;
        }
        function repeat(text, times) {
            var res = '';
            while (times-- > 0) {
                res += text;
            }
            return res;
        }
        function slice(text, limit) {
            return typeof limit === 'number' ? text.slice(0, limit) : text;
        }
        function containsArray(jsonText) {
            return /^\s*\[/.test(jsonText);
        }
        function jsonUtils_hasOwnProperty(object, key) {
            return Object.prototype.hasOwnProperty.call(object, key);
        }
        var util = __webpack_require__(0);
        var constants = __webpack_require__(2);
        var DEFAULT_DESCRIPTION = 'Enter a <a href="http://jmespath.org" target="_blank">JMESPath</a> query to filter, sort, or transform the JSON data.<br/>' + 'To learn JMESPath, go to <a href="http://jmespath.org/tutorial.html" target="_blank">the interactive tutorial</a>.';
        function showTransformModal(_ref) {
            var container = _ref.container
              , json = _ref.json
              , _ref$queryDescription = _ref.queryDescription
              , queryDescription = _ref$queryDescription === void 0 ? DEFAULT_DESCRIPTION : _ref$queryDescription
              , createQuery = _ref.createQuery
              , executeQuery = _ref.executeQuery
              , onTransform = _ref.onTransform;
            var value = json;
            var content = '<label class="pico-modal-contents">' + '<div class="pico-modal-header">' + Object(i18n["c"])('transform') + '</div>' + '<p>' + queryDescription + '</p>' + '<div class="jsoneditor-jmespath-label">' + Object(i18n["c"])('transformWizardLabel') + ' </div>' + '<div id="wizard" class="jsoneditor-jmespath-block jsoneditor-jmespath-wizard">' + '  <table class="jsoneditor-jmespath-wizard-table">' + '    <tbody>' + '      <tr>' + '        <th>' + Object(i18n["c"])('transformWizardFilter') + '</th>' + '        <td class="jsoneditor-jmespath-filter">' + '          <div class="jsoneditor-inline jsoneditor-jmespath-filter-field" >' + '            <select id="filterField">' + '            </select>' + '          </div>' + '          <div class="jsoneditor-inline jsoneditor-jmespath-filter-relation" >' + '            <select id="filterRelation">' + '              <option value="==">==</option>' + '              <option value="!=">!=</option>' + '              <option value="<">&lt;</option>' + '              <option value="<=">&lt;=</option>' + '              <option value=">">&gt;</option>' + '              <option value=">=">&gt;=</option>' + '            </select>' + '          </div>' + '          <div class="jsoneditor-inline jsoneditor-jmespath-filter-value" >' + '            <input type="text" class="value" placeholder="value..." id="filterValue" />' + '          </div>' + '        </td>' + '      </tr>' + '      <tr>' + '        <th>' + Object(i18n["c"])('transformWizardSortBy') + '</th>' + '        <td class="jsoneditor-jmespath-filter">' + '          <div class="jsoneditor-inline jsoneditor-jmespath-sort-field">' + '            <select id="sortField">' + '            </select>' + '          </div>' + '          <div class="jsoneditor-inline jsoneditor-jmespath-sort-order" >' + '            <select id="sortOrder">' + '              <option value="asc">Ascending</option>' + '              <option value="desc">Descending</option>' + '            </select>' + '          </div>' + '        </td>' + '      </tr>' + '      <tr id="selectFieldsPart">' + '        <th>' + Object(i18n["c"])('transformWizardSelectFields') + '</th>' + '        <td class="jsoneditor-jmespath-filter">' + '          <select class="jsoneditor-jmespath-select-fields" id="selectFields" multiple></select>' + '        </td>' + '      </tr>' + '    </tbody>' + '  </table>' + '</div>' + '<div class="jsoneditor-jmespath-label">' + Object(i18n["c"])('transformQueryLabel') + ' </div>' + '<div class="jsoneditor-jmespath-block">' + '  <textarea id="query" ' + '            rows="4" ' + '            autocomplete="off" ' + '            autocorrect="off" ' + '            autocapitalize="off" ' + '            spellcheck="false"' + '            title="' + Object(i18n["c"])('transformQueryTitle') + '">[*]</textarea>' + '</div>' + '<div class="jsoneditor-jmespath-label">' + Object(i18n["c"])('transformPreviewLabel') + ' </div>' + '<div class="jsoneditor-jmespath-block">' + '  <textarea id="preview" ' + '      class="jsoneditor-transform-preview"' + '      readonly> </textarea>' + '</div>' + '<div class="jsoneditor-jmespath-block jsoneditor-modal-actions">' + '  <input type="submit" id="ok" value="' + Object(i18n["c"])('ok') + '" autofocus />' + '</div>' + '</div>';
            picoModal_default()({
                parent: container,
                content: content,
                overlayClass: 'jsoneditor-modal-overlay',
                overlayStyles: {
                    backgroundColor: 'rgb(1,1,1)',
                    opacity: 0.3
                },
                modalClass: 'jsoneditor-modal jsoneditor-modal-transform',
                focus: false
            }).afterCreate(function(modal) {
                var elem = modal.modalElem();
                var wizard = elem.querySelector('#wizard');
                var ok = elem.querySelector('#ok');
                var filterField = elem.querySelector('#filterField');
                var filterRelation = elem.querySelector('#filterRelation');
                var filterValue = elem.querySelector('#filterValue');
                var sortField = elem.querySelector('#sortField');
                var sortOrder = elem.querySelector('#sortOrder');
                var selectFields = elem.querySelector('#selectFields');
                var query = elem.querySelector('#query');
                var preview = elem.querySelector('#preview');
                if (!Array.isArray(value)) {
                    wizard.style.fontStyle = 'italic';
                    wizard.textContent = '(wizard not available for objects, only for arrays)';
                }
                var sortablePaths = Object(util["getChildPaths"])(json);
                sortablePaths.forEach(function(path) {
                    var formattedPath = preprocessPath(path);
                    var filterOption = document.createElement('option');
                    filterOption.text = formattedPath;
                    filterOption.value = formattedPath;
                    filterField.appendChild(filterOption);
                    var sortOption = document.createElement('option');
                    sortOption.text = formattedPath;
                    sortOption.value = formattedPath;
                    sortField.appendChild(sortOption);
                });
                var selectablePaths = Object(util["getChildPaths"])(json, true).filter(function(path) {
                    return path !== '';
                });
                if (selectablePaths.length > 0) {
                    selectablePaths.forEach(function(path) {
                        var formattedPath = preprocessPath(path);
                        var option = document.createElement('option');
                        option.text = formattedPath;
                        option.value = formattedPath;
                        selectFields.appendChild(option);
                    });
                } else {
                    var selectFieldsPart = elem.querySelector('#selectFieldsPart');
                    if (selectFieldsPart) {
                        selectFieldsPart.style.display = 'none';
                    }
                }
                var selectrFilterField = new selectr_default.a(filterField,{
                    defaultSelected: false,
                    clearable: true,
                    allowDeselect: true,
                    placeholder: 'field...'
                });
                var selectrFilterRelation = new selectr_default.a(filterRelation,{
                    defaultSelected: false,
                    clearable: true,
                    allowDeselect: true,
                    placeholder: 'compare...'
                });
                var selectrSortField = new selectr_default.a(sortField,{
                    defaultSelected: false,
                    clearable: true,
                    allowDeselect: true,
                    placeholder: 'field...'
                });
                var selectrSortOrder = new selectr_default.a(sortOrder,{
                    defaultSelected: false,
                    clearable: true,
                    allowDeselect: true,
                    placeholder: 'order...'
                });
                var selectrSelectFields = new selectr_default.a(selectFields,{
                    multiple: true,
                    clearable: true,
                    defaultSelected: false,
                    placeholder: 'select fields...'
                });
                selectrFilterField.on('selectr.change', generateQueryFromWizard);
                selectrFilterRelation.on('selectr.change', generateQueryFromWizard);
                filterValue.oninput = generateQueryFromWizard;
                selectrSortField.on('selectr.change', generateQueryFromWizard);
                selectrSortOrder.on('selectr.change', generateQueryFromWizard);
                selectrSelectFields.on('selectr.change', generateQueryFromWizard);
                elem.querySelector('.pico-modal-contents').onclick = function(event) {
                    if (event.target.nodeName !== 'A') {
                        event.preventDefault();
                    }
                }
                ;
                function preprocessPath(path) {
                    return path === '' ? '@' : path[0] === '.' ? path.slice(1) : path;
                }
                function updatePreview() {
                    try {
                        var transformed = executeQuery(value, query.value);
                        preview.className = 'jsoneditor-transform-preview';
                        preview.value = stringifyPartial(transformed, 2, constants["b"]);
                        ok.disabled = false;
                    } catch (err) {
                        preview.className = 'jsoneditor-transform-preview jsoneditor-error';
                        preview.value = err.toString();
                        ok.disabled = true;
                    }
                }
                var debouncedUpdatePreview = Object(util["debounce"])(updatePreview, 300);
                function tryCreateQuery(json, queryOptions) {
                    try {
                        query.value = createQuery(json, queryOptions);
                        ok.disabled = false;
                        debouncedUpdatePreview();
                    } catch (err) {
                        var message = 'Error: an error happened when executing "createQuery": ' + (err.message || err.toString());
                        query.value = '';
                        ok.disabled = true;
                        preview.className = 'jsoneditor-transform-preview jsoneditor-error';
                        preview.value = message;
                    }
                }
                function generateQueryFromWizard() {
                    var queryOptions = {};
                    if (filterField.value && filterRelation.value && filterValue.value) {
                        queryOptions.filter = {
                            field: filterField.value,
                            relation: filterRelation.value,
                            value: filterValue.value
                        };
                    }
                    if (sortField.value && sortOrder.value) {
                        queryOptions.sort = {
                            field: sortField.value,
                            direction: sortOrder.value
                        };
                    }
                    if (selectFields.value) {
                        var fields = [];
                        for (var i = 0; i < selectFields.options.length; i++) {
                            if (selectFields.options[i].selected) {
                                var selectedField = selectFields.options[i].value;
                                fields.push(selectedField);
                            }
                        }
                        queryOptions.projection = {
                            fields: fields
                        };
                    }
                    tryCreateQuery(json, queryOptions);
                }
                query.oninput = debouncedUpdatePreview;
                ok.onclick = function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    modal.close();
                    onTransform(query.value);
                }
                ;
                tryCreateQuery(json, {});
                setTimeout(function() {
                    query.select();
                    query.focus();
                    query.selectionStart = 3;
                    query.selectionEnd = 3;
                });
            }).afterClose(function(modal) {
                modal.destroy();
            }).show();
        }
    }
    ), (function(module, exports, __webpack_require__) {
        !function(n, e) {
            true ? module.exports = e() : undefined
        }(this, function() {
            "use strict";
            var r, t, n = (r = function(n, e) {
                return (r = Object.setPrototypeOf || {
                    __proto__: []
                }instanceof Array && function(n, e) {
                    n.__proto__ = e
                }
                || function(n, e) {
                    for (var t in e)
                        Object.prototype.hasOwnProperty.call(e, t) && (n[t] = e[t])
                }
                )(n, e)
            }
            ,
            function(n, e) {
                function t() {
                    this.constructor = n
                }
                r(n, e),
                n.prototype = null === e ? Object.create(e) : (t.prototype = e.prototype,
                new t)
            }
            ), i = (t = SyntaxError,
            n(e, t),
            e);
            function e(n, e) {
                n = t.call(this, n + " (char " + e + ")") || this;
                return n.char = e,
                n
            }
            var f = ["'", "‘", "’", "`", "´"]
              , o = ['"', "“", "”"];
            function u(n) {
                return /^[a-zA-Z_]$/.test(n)
            }
            function c(n) {
                return "0" <= n && n <= "9"
            }
            function l(n) {
                return " " === n || "\t" === n || "\n" === n || "\r" === n
            }
            function s(n) {
                return " " === n || " " <= n && n <= " " || " " === n || " " === n || "　" === n
            }
            function a(n) {
                return f.includes(n) ? "'" : o.includes(n) ? '"' : n
            }
            function d(n, e) {
                e = n.lastIndexOf(e);
                return -1 !== e ? n.substring(0, e) + n.substring(e + 1) : n
            }
            function h(n, e) {
                return n.replace(/\s*$/, function(n) {
                    return e + n
                })
            }
            var p = 0
              , b = 1
              , w = 2
              , g = 3
              , v = 4
              , x = 5
              , y = 6
              , _ = {
                "": !0,
                "{": !0,
                "}": !0,
                "[": !0,
                "]": !0,
                ":": !0,
                ",": !0,
                "(": !0,
                ")": !0,
                ";": !0,
                "+": !0
            }
              , O = {
                '"': '"',
                "\\": "\\",
                "/": "/",
                b: "\b",
                f: "\f",
                n: "\n",
                r: "\r",
                t: "\t"
            }
              , m = {
                "\b": "\\b",
                "\f": "\\f",
                "\n": "\\n",
                "\r": "\\r",
                "\t": "\\t"
            }
              , I = {
                null: "null",
                true: "true",
                false: "false"
            }
              , j = {
                None: "null",
                True: "true",
                False: "false"
            }
              , k = ""
              , A = ""
              , $ = 0
              , E = ""
              , T = ""
              , F = y;
            function P() {
                $++,
                E = k.charAt($)
            }
            function S() {
                return F === p && ("[" === T || "{" === T) || F === w || F === b || F === g
            }
            function U() {
                A += T,
                F = y,
                T = "",
                function() {
                    if (_[E])
                        return F = p,
                        T = E,
                        P();
                    !function() {
                        if (c(E) || "-" === E) {
                            if (F = b,
                            "-" === E) {
                                if (T += E,
                                P(),
                                !c(E))
                                    throw new i("Invalid number, digit expected",$)
                            } else
                                "0" === E && (T += E,
                                P());
                            for (; c(E); )
                                T += E,
                                P();
                            if ("." === E) {
                                if (T += E,
                                P(),
                                !c(E))
                                    throw new i("Invalid number, digit expected",$);
                                for (; c(E); )
                                    T += E,
                                    P()
                            }
                            if ("e" === E || "E" === E) {
                                if (T += E,
                                P(),
                                "+" !== E && "-" !== E || (T += E,
                                P()),
                                !c(E))
                                    throw new i("Invalid number, digit expected",$);
                                for (; c(E); )
                                    T += E,
                                    P()
                            }
                            return
                        }
                        !function() {
                            if (function(n) {
                                return f.includes(n) || o.includes(n)
                            }(E)) {
                                var n = a(E);
                                for (T += '"',
                                F = w,
                                P(); "" !== E && a(E) !== n; )
                                    if ("\\" === E)
                                        if (P(),
                                        void 0 !== O[E])
                                            T += "\\" + E,
                                            P();
                                        else if ("u" === E) {
                                            T += "\\u",
                                            P();
                                            for (var e = 0; e < 4; e++) {
                                                if (!/^[0-9a-fA-F]$/.test(E))
                                                    throw new i("Invalid unicode character",$ - T.length);
                                                T += E,
                                                P()
                                            }
                                        } else {
                                            if ("'" !== E)
                                                throw new i('Invalid escape character "\\' + E + '"',$);
                                            T += "'",
                                            P()
                                        }
                                    else
                                        m[E] ? T += m[E] : T += '"' === E ? '\\"' : E,
                                        P();
                                if (a(E) !== n)
                                    throw new i("End of string expected",$ - T.length);
                                return T += '"',
                                P()
                            }
                            !function() {
                                if (u(E)) {
                                    for (F = g; u(E) || c(E) || "$" === E; )
                                        T += E,
                                        P();
                                    return
                                }
                                !function() {
                                    if (l(E) || s(E)) {
                                        for (F = v; l(E) || s(E); )
                                            T += E,
                                            P();
                                        return
                                    }
                                    !function() {
                                        if ("/" === E && "*" === k[$ + 1]) {
                                            for (F = x; "" !== E && ("*" !== E || "*" === E && "/" !== k[$ + 1]); )
                                                T += E,
                                                P();
                                            return "*" === E && "/" === k[$ + 1] && (T += E,
                                            P(),
                                            T += E,
                                            P())
                                        }
                                        if ("/" === E && "/" === k[$ + 1]) {
                                            for (F = x; "" !== E && "\n" !== E; )
                                                T += E,
                                                P();
                                            return
                                        }
                                        !function() {
                                            F = y;
                                            for (; "" !== E; )
                                                T += E,
                                                P();
                                            throw new i('Syntax error in part "' + T + '"',$ - T.length)
                                        }()
                                    }()
                                }()
                            }()
                        }()
                    }()
                }(),
                F === v && (T = function(n) {
                    for (var e = "", t = 0; t < n.length; t++) {
                        var r = n[t];
                        e += s(r) ? " " : r
                    }
                    return e
                }(T),
                U()),
                F === x && (F = y,
                T = "",
                U())
            }
            function z() {
                if (F !== p || "{" !== T)
                    !function() {
                        if (F === p && "[" === T) {
                            if (U(),
                            F === p && "]" === T)
                                return U();
                            for (; ; )
                                if (z(),
                                F === p && "," === T) {
                                    if (U(),
                                    F === p && "]" === T) {
                                        A = d(A, ",");
                                        break
                                    }
                                } else {
                                    if (!S())
                                        break;
                                    A = h(A, ",")
                                }
                            return F === p && "]" === T ? U() : A = h(A, "]")
                        }
                        !function() {
                            if (F === w) {
                                for (U(); F === p && "+" === T; ) {
                                    var n;
                                    T = "",
                                    U(),
                                    F === w && (n = A.lastIndexOf('"'),
                                    A = A.substring(0, n) + T.substring(1),
                                    T = "",
                                    U())
                                }
                                return
                            }
                            !function() {
                                if (F === b)
                                    return U();
                                !function() {
                                    if (F === g) {
                                        if (I[T])
                                            return U();
                                        if (j[T])
                                            return T = j[T],
                                            U();
                                        var n = T
                                          , e = A.length;
                                        if (T = "",
                                        U(),
                                        F === p && "(" === T)
                                            return T = "",
                                            U(),
                                            z(),
                                            F === p && ")" === T && (T = "",
                                            U(),
                                            F === p && ";" === T && (T = "",
                                            U()));
                                        for (A = function(n, e, t) {
                                            return n.substring(0, t) + e + n.substring(t)
                                        }(A, '"' + n, e); F === g || F === b; )
                                            U();
                                        return A += '"'
                                    }
                                    !function() {
                                        throw new i("" === T ? "Unexpected end of json string" : "Value expected",$ - T.length)
                                    }()
                                }()
                            }()
                        }()
                    }();
                else if (U(),
                F !== p || "}" !== T) {
                    for (; ; ) {
                        if (F !== g && F !== b || (F = w,
                        T = '"' + T + '"'),
                        F !== w)
                            throw new i("Object key expected",$ - T.length);
                        if (U(),
                        F === p && ":" === T)
                            U();
                        else {
                            if (!S())
                                throw new i("Colon expected",$ - T.length);
                            A = h(A, ":")
                        }
                        if (z(),
                        F === p && "," === T) {
                            if (U(),
                            F === p && "}" === T) {
                                A = d(A, ",");
                                break
                            }
                        } else {
                            if (F !== w && F !== b && F !== g)
                                break;
                            A = h(A, ",")
                        }
                    }
                    F === p && "}" === T ? U() : A = h(A, "}")
                } else
                    U()
            }
            return function(n) {
                if (A = "",
                $ = 0,
                E = (k = n).charAt(0),
                T = "",
                F = y,
                U(),
                n = F === p && "{" === T,
                z(),
                "" === T)
                    return A;
                if (n && S()) {
                    for (; S(); )
                        A = h(A, ","),
                        z();
                    return "[\n" + A + "\n]"
                }
                throw new i("Unexpected characters",$ - T.length)
            }
        });
    }
    ), (function(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        __webpack_require__.d(__webpack_exports__, "a", function() {
            return FocusTracker;
        });
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value"in descriptor)
                    descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps)
                _defineProperties(Constructor.prototype, protoProps);
            if (staticProps)
                _defineProperties(Constructor, staticProps);
            return Constructor;
        }
        var FocusTracker = function() {
            function FocusTracker(config) {
                _classCallCheck(this, FocusTracker);
                this.target = config.target || null;
                if (!this.target) {
                    throw new Error('FocusTracker constructor called without a "target" to track.');
                }
                this.onFocus = typeof config.onFocus === 'function' ? config.onFocus : null;
                this.onBlur = typeof config.onBlur === 'function' ? config.onBlur : null;
                this._onClick = this._onEvent.bind(this);
                this._onKeyUp = function(event) {
                    if (event.which === 9 || event.keyCode === 9) {
                        this._onEvent(event);
                    }
                }
                .bind(this);
                this.focusFlag = false;
                this.firstEventFlag = true;
                if (this.onFocus || this.onBlur) {
                    document.addEventListener('click', this._onClick);
                    document.addEventListener('keyup', this._onKeyUp);
                }
            }
            _createClass(FocusTracker, [{
                key: "destroy",
                value: function destroy() {
                    document.removeEventListener('click', this._onClick);
                    document.removeEventListener('keyup', this._onKeyUp);
                    this._onEvent({
                        target: document.body
                    });
                }
            }, {
                key: "_onEvent",
                value: function _onEvent(event) {
                    var target = event.target;
                    var focusFlag;
                    if (target === this.target) {
                        focusFlag = true;
                    } else if (this.target.contains(target) || this.target.contains(document.activeElement)) {
                        focusFlag = true;
                    } else {
                        focusFlag = false;
                    }
                    if (focusFlag) {
                        if (!this.focusFlag) {
                            if (this.onFocus) {
                                this.onFocus({
                                    type: 'focus',
                                    target: this.target
                                });
                            }
                            this.focusFlag = true;
                        }
                    } else {
                        if (this.focusFlag || this.firstEventFlag) {
                            if (this.onBlur) {
                                this.onBlur({
                                    type: 'blur',
                                    target: this.target
                                });
                            }
                            this.focusFlag = false;
                            if (this.firstEventFlag) {
                                this.firstEventFlag = false;
                            }
                        }
                    }
                }
            }]);
            return FocusTracker;
        }();
    }
    ), (function(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        __webpack_require__.d(__webpack_exports__, "a", function() {
            return ModeSwitcher;
        });
        var _ContextMenu__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(3);
        var _i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(1);
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value"in descriptor)
                    descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps)
                _defineProperties(Constructor.prototype, protoProps);
            if (staticProps)
                _defineProperties(Constructor, staticProps);
            return Constructor;
        }
        var ModeSwitcher = function() {
            function ModeSwitcher(container, modes, current, onSwitch) {
                _classCallCheck(this, ModeSwitcher);
                var availableModes = {
                    code: {
                        text: Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('modeCodeText'),
                        title: Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('modeCodeTitle'),
                        click: function click() {
                            onSwitch('code');
                        }
                    },
                    form: {
                        text: Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('modeFormText'),
                        title: Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('modeFormTitle'),
                        click: function click() {
                            onSwitch('form');
                        }
                    },
                    text: {
                        text: Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('modeTextText'),
                        title: Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('modeTextTitle'),
                        click: function click() {
                            onSwitch('text');
                        }
                    },
                    tree: {
                        text: Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('modeTreeText'),
                        title: Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('modeTreeTitle'),
                        click: function click() {
                            onSwitch('tree');
                        }
                    },
                    view: {
                        text: Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('modeViewText'),
                        title: Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('modeViewTitle'),
                        click: function click() {
                            onSwitch('view');
                        }
                    },
                    preview: {
                        text: Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('modePreviewText'),
                        title: Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('modePreviewTitle'),
                        click: function click() {
                            onSwitch('preview');
                        }
                    }
                };
                var items = [];
                for (var i = 0; i < modes.length; i++) {
                    var mode = modes[i];
                    var item = availableModes[mode];
                    if (!item) {
                        throw new Error('Unknown mode "' + mode + '"');
                    }
                    item.className = 'jsoneditor-type-modes' + (current === mode ? ' jsoneditor-selected' : '');
                    items.push(item);
                }
                var currentMode = availableModes[current];
                if (!currentMode) {
                    throw new Error('Unknown mode "' + current + '"');
                }
                var currentTitle = currentMode.text;
                var box = document.createElement('button');
                box.type = 'button';
                box.className = 'jsoneditor-modes jsoneditor-separator';
                box.textContent = currentTitle + " \u25BE";
                box.title = Object(_i18n__WEBPACK_IMPORTED_MODULE_1__["c"])('modeEditorTitle');
                box.onclick = function() {
                    var menu = new _ContextMenu__WEBPACK_IMPORTED_MODULE_0__["a"](items);
                    menu.show(box, container);
                }
                ;
                var frame = document.createElement('div');
                frame.className = 'jsoneditor-modes';
                frame.style.position = 'relative';
                frame.appendChild(box);
                container.appendChild(frame);
                this.dom = {
                    container: container,
                    box: box,
                    frame: frame
                };
            }
            _createClass(ModeSwitcher, [{
                key: "focus",
                value: function focus() {
                    this.dom.box.focus();
                }
            }, {
                key: "destroy",
                value: function destroy() {
                    if (this.dom && this.dom.frame && this.dom.frame.parentNode) {
                        this.dom.frame.parentNode.removeChild(this.dom.frame);
                    }
                    this.dom = null;
                }
            }]);
            return ModeSwitcher;
        }();
    }
    ), (function(module, exports, __webpack_require__) {
        "use strict";
        /*!
* Selectr 2.4.0
* https://github.com/Mobius1/Selectr
*
* Released under the MIT license
*/
        var defaultConfig = {
            defaultSelected: true,
            width: "auto",
            disabled: false,
            searchable: true,
            clearable: false,
            sortSelected: false,
            allowDeselect: false,
            closeOnScroll: false,
            nativeDropdown: false,
            placeholder: "Select an option...",
            taggable: false,
            tagPlaceholder: "Enter a tag..."
        };
        var Events = function Events() {};
        Events.prototype = {
            on: function on(event, func) {
                this._events = this._events || {};
                this._events[event] = this._events[event] || [];
                this._events[event].push(func);
            },
            off: function off(event, func) {
                this._events = this._events || {};
                if (event in this._events === false)
                    return;
                this._events[event].splice(this._events[event].indexOf(func), 1);
            },
            emit: function emit(event) {
                this._events = this._events || {};
                if (event in this._events === false)
                    return;
                for (var i = 0; i < this._events[event].length; i++) {
                    this._events[event][i].apply(this, Array.prototype.slice.call(arguments, 1));
                }
            }
        };
        Events.mixin = function(obj) {
            var props = ['on', 'off', 'emit'];
            for (var i = 0; i < props.length; i++) {
                if (typeof obj === 'function') {
                    obj.prototype[props[i]] = Events.prototype[props[i]];
                } else {
                    obj[props[i]] = Events.prototype[props[i]];
                }
            }
            return obj;
        }
        ;
        var util = {
            extend: function extend(src, props) {
                props = props || {};
                var p;
                for (p in src) {
                    if (src.hasOwnProperty(p)) {
                        if (!props.hasOwnProperty(p)) {
                            props[p] = src[p];
                        }
                    }
                }
                return props;
            },
            each: function each(a, b, c) {
                if ("[object Object]" === Object.prototype.toString.call(a)) {
                    for (var d in a) {
                        if (Object.prototype.hasOwnProperty.call(a, d)) {
                            b.call(c, d, a[d], a);
                        }
                    }
                } else {
                    for (var e = 0, f = a.length; e < f; e++) {
                        b.call(c, e, a[e], a);
                    }
                }
            },
            createElement: function createElement(e, a) {
                var d = document
                  , el = d.createElement(e);
                if (a && "[object Object]" === Object.prototype.toString.call(a)) {
                    var i;
                    for (i in a) {
                        if (i in el)
                            el[i] = a[i];
                        else if ("html" === i)
                            el.textContent = a[i];
                        else if ("text" === i) {
                            var t = d.createTextNode(a[i]);
                            el.appendChild(t);
                        } else
                            el.setAttribute(i, a[i]);
                    }
                }
                return el;
            },
            hasClass: function hasClass(a, b) {
                if (a)
                    return a.classList ? a.classList.contains(b) : !!a.className && !!a.className.match(new RegExp("(\\s|^)" + b + "(\\s|$)"));
            },
            addClass: function addClass(a, b) {
                if (!util.hasClass(a, b)) {
                    if (a.classList) {
                        a.classList.add(b);
                    } else {
                        a.className = a.className.trim() + " " + b;
                    }
                }
            },
            removeClass: function removeClass(a, b) {
                if (util.hasClass(a, b)) {
                    if (a.classList) {
                        a.classList.remove(b);
                    } else {
                        a.className = a.className.replace(new RegExp("(^|\\s)" + b.split(" ").join("|") + "(\\s|$)","gi"), " ");
                    }
                }
            },
            closest: function closest(el, fn) {
                return el && el !== document.body && (fn(el) ? el : util.closest(el.parentNode, fn));
            },
            isInt: function isInt(val) {
                return typeof val === 'number' && isFinite(val) && Math.floor(val) === val;
            },
            debounce: function debounce(a, b, c) {
                var d;
                return function() {
                    var e = this
                      , f = arguments
                      , g = function g() {
                        d = null;
                        if (!c)
                            a.apply(e, f);
                    }
                      , h = c && !d;
                    clearTimeout(d);
                    d = setTimeout(g, b);
                    if (h) {
                        a.apply(e, f);
                    }
                }
                ;
            },
            rect: function rect(el, abs) {
                var w = window;
                var r = el.getBoundingClientRect();
                var x = abs ? w.pageXOffset : 0;
                var y = abs ? w.pageYOffset : 0;
                return {
                    bottom: r.bottom + y,
                    height: r.height,
                    left: r.left + x,
                    right: r.right + x,
                    top: r.top + y,
                    width: r.width
                };
            },
            includes: function includes(a, b) {
                return a.indexOf(b) > -1;
            },
            truncate: function truncate(el) {
                while (el.firstChild) {
                    el.removeChild(el.firstChild);
                }
            }
        };
        function isset(obj, prop) {
            return obj.hasOwnProperty(prop) && (obj[prop] === true || obj[prop].length);
        }
        function appendItem(item, parent, custom) {
            if (item.parentNode) {
                if (!item.parentNode.parentNode) {
                    parent.appendChild(item.parentNode);
                }
            } else {
                parent.appendChild(item);
            }
            util.removeClass(item, "excluded");
            if (!custom) {
                item.textContent = item.textContent + '';
            }
        }
        var render = function render() {
            if (this.items.length) {
                var f = document.createDocumentFragment();
                if (this.config.pagination) {
                    var pages = this.pages.slice(0, this.pageIndex);
                    util.each(pages, function(i, items) {
                        util.each(items, function(j, item) {
                            appendItem(item, f, this.customOption);
                        }, this);
                    }, this);
                } else {
                    util.each(this.items, function(i, item) {
                        appendItem(item, f, this.customOption);
                    }, this);
                }
                if (f.childElementCount) {
                    util.removeClass(this.items[this.navIndex], "active");
                    this.navIndex = f.querySelector(".selectr-option").idx;
                    util.addClass(this.items[this.navIndex], "active");
                }
                this.tree.appendChild(f);
            }
        };
        var dismiss = function dismiss(e) {
            var target = e.target;
            if (!this.container.contains(target) && (this.opened || util.hasClass(this.container, "notice"))) {
                this.close();
            }
        };
        var createItem = function createItem(option, data) {
            data = data || option;
            var content = this.customOption ? this.config.renderOption(data) : option.textContent;
            var opt = util.createElement("li", {
                "class": "selectr-option",
                html: content,
                role: "treeitem",
                "aria-selected": false
            });
            opt.idx = option.idx;
            this.items.push(opt);
            if (option.defaultSelected) {
                this.defaultSelected.push(option.idx);
            }
            if (option.disabled) {
                opt.disabled = true;
                util.addClass(opt, "disabled");
            }
            return opt;
        };
        var build = function build() {
            this.requiresPagination = this.config.pagination && this.config.pagination > 0;
            if (isset(this.config, "width")) {
                if (util.isInt(this.config.width)) {
                    this.width = this.config.width + "px";
                } else {
                    if (this.config.width === "auto") {
                        this.width = "100%";
                    } else if (util.includes(this.config.width, "%")) {
                        this.width = this.config.width;
                    }
                }
            }
            this.container = util.createElement("div", {
                "class": "selectr-container"
            });
            if (this.config.customClass) {
                util.addClass(this.container, this.config.customClass);
            }
            if (this.mobileDevice) {
                util.addClass(this.container, "selectr-mobile");
            } else {
                util.addClass(this.container, "selectr-desktop");
            }
            this.el.tabIndex = -1;
            if (this.config.nativeDropdown || this.mobileDevice) {
                util.addClass(this.el, "selectr-visible");
            } else {
                util.addClass(this.el, "selectr-hidden");
            }
            this.selected = util.createElement("div", {
                "class": "selectr-selected",
                disabled: this.disabled,
                tabIndex: 1,
                "aria-expanded": false
            });
            this.label = util.createElement(this.el.multiple ? "ul" : "span", {
                "class": "selectr-label"
            });
            var dropdown = util.createElement("div", {
                "class": "selectr-options-container"
            });
            this.tree = util.createElement("ul", {
                "class": "selectr-options",
                role: "tree",
                "aria-hidden": true,
                "aria-expanded": false
            });
            this.notice = util.createElement("div", {
                "class": "selectr-notice"
            });
            this.el.setAttribute("aria-hidden", true);
            if (this.disabled) {
                this.el.disabled = true;
            }
            if (this.el.multiple) {
                util.addClass(this.label, "selectr-tags");
                util.addClass(this.container, "multiple");
                this.tags = [];
                this.selectedValues = this.getSelectedProperties('value');
                this.selectedIndexes = this.getSelectedProperties('idx');
            }
            this.selected.appendChild(this.label);
            if (this.config.clearable) {
                this.selectClear = util.createElement("button", {
                    "class": "selectr-clear",
                    type: "button"
                });
                this.container.appendChild(this.selectClear);
                util.addClass(this.container, "clearable");
            }
            if (this.config.taggable) {
                var li = util.createElement('li', {
                    "class": 'input-tag'
                });
                this.input = util.createElement("input", {
                    "class": "selectr-tag-input",
                    placeholder: this.config.tagPlaceholder,
                    tagIndex: 0,
                    autocomplete: "off",
                    autocorrect: "off",
                    autocapitalize: "off",
                    spellcheck: "false",
                    role: "textbox",
                    type: "search"
                });
                li.appendChild(this.input);
                this.label.appendChild(li);
                util.addClass(this.container, "taggable");
                this.tagSeperators = [","];
                if (this.config.tagSeperators) {
                    this.tagSeperators = this.tagSeperators.concat(this.config.tagSeperators);
                }
            }
            if (this.config.searchable) {
                this.input = util.createElement("input", {
                    "class": "selectr-input",
                    tagIndex: -1,
                    autocomplete: "off",
                    autocorrect: "off",
                    autocapitalize: "off",
                    spellcheck: "false",
                    role: "textbox",
                    type: "search"
                });
                this.inputClear = util.createElement("button", {
                    "class": "selectr-input-clear",
                    type: "button"
                });
                this.inputContainer = util.createElement("div", {
                    "class": "selectr-input-container"
                });
                this.inputContainer.appendChild(this.input);
                this.inputContainer.appendChild(this.inputClear);
                dropdown.appendChild(this.inputContainer);
            }
            dropdown.appendChild(this.notice);
            dropdown.appendChild(this.tree);
            this.items = [];
            this.options = [];
            if (this.el.options.length) {
                this.options = [].slice.call(this.el.options);
            }
            var group = false
              , j = 0;
            if (this.el.children.length) {
                util.each(this.el.children, function(i, element) {
                    if (element.nodeName === "OPTGROUP") {
                        group = util.createElement("ul", {
                            "class": "selectr-optgroup",
                            role: "group",
                            html: "<li class='selectr-optgroup--label'>" + element.label + "</li>"
                        });
                        util.each(element.children, function(x, el) {
                            el.idx = j;
                            group.appendChild(createItem.call(this, el, group));
                            j++;
                        }, this);
                    } else {
                        element.idx = j;
                        createItem.call(this, element);
                        j++;
                    }
                }, this);
            }
            if (this.config.data && Array.isArray(this.config.data)) {
                this.data = [];
                var optgroup = false, option;
                group = false;
                j = 0;
                util.each(this.config.data, function(i, opt) {
                    if (isset(opt, "children")) {
                        optgroup = util.createElement("optgroup", {
                            label: opt.text
                        });
                        group = util.createElement("ul", {
                            "class": "selectr-optgroup",
                            role: "group",
                            html: "<li class='selectr-optgroup--label'>" + opt.text + "</li>"
                        });
                        util.each(opt.children, function(x, data) {
                            option = new Option(data.text,data.value,false,data.hasOwnProperty("selected") && data.selected === true);
                            option.disabled = isset(data, "disabled");
                            this.options.push(option);
                            optgroup.appendChild(option);
                            option.idx = j;
                            group.appendChild(createItem.call(this, option, data));
                            this.data[j] = data;
                            j++;
                        }, this);
                    } else {
                        option = new Option(opt.text,opt.value,false,opt.hasOwnProperty("selected") && opt.selected === true);
                        option.disabled = isset(opt, "disabled");
                        this.options.push(option);
                        option.idx = j;
                        createItem.call(this, option, opt);
                        this.data[j] = opt;
                        j++;
                    }
                }, this);
            }
            this.setSelected(true);
            var first;
            this.navIndex = 0;
            for (var i = 0; i < this.items.length; i++) {
                first = this.items[i];
                if (!util.hasClass(first, "disabled")) {
                    util.addClass(first, "active");
                    this.navIndex = i;
                    break;
                }
            }
            if (this.requiresPagination) {
                this.pageIndex = 1;
                this.paginate();
            }
            this.container.appendChild(this.selected);
            this.container.appendChild(dropdown);
            this.placeEl = util.createElement("div", {
                "class": "selectr-placeholder"
            });
            this.setPlaceholder();
            this.selected.appendChild(this.placeEl);
            if (this.disabled) {
                this.disable();
            }
            this.el.parentNode.insertBefore(this.container, this.el);
            this.container.appendChild(this.el);
        };
        var navigate = function navigate(e) {
            e = e || window.event;
            if (!this.items.length || !this.opened || !util.includes([13, 38, 40], e.which)) {
                this.navigating = false;
                return;
            }
            e.preventDefault();
            if (e.which === 13) {
                if (this.config.taggable && this.input.value.length > 0) {
                    return false;
                }
                return this.change(this.navIndex);
            }
            var direction, prevEl = this.items[this.navIndex];
            switch (e.which) {
            case 38:
                direction = 0;
                if (this.navIndex > 0) {
                    this.navIndex--;
                }
                break;
            case 40:
                direction = 1;
                if (this.navIndex < this.items.length - 1) {
                    this.navIndex++;
                }
            }
            this.navigating = true;
            while (util.hasClass(this.items[this.navIndex], "disabled") || util.hasClass(this.items[this.navIndex], "excluded")) {
                if (direction) {
                    this.navIndex++;
                } else {
                    this.navIndex--;
                }
                if (this.searching) {
                    if (this.navIndex > this.tree.lastElementChild.idx) {
                        this.navIndex = this.tree.lastElementChild.idx;
                        break;
                    } else if (this.navIndex < this.tree.firstElementChild.idx) {
                        this.navIndex = this.tree.firstElementChild.idx;
                        break;
                    }
                }
            }
            var r = util.rect(this.items[this.navIndex]);
            if (!direction) {
                if (this.navIndex === 0) {
                    this.tree.scrollTop = 0;
                } else if (r.top - this.optsRect.top < 0) {
                    this.tree.scrollTop = this.tree.scrollTop + (r.top - this.optsRect.top);
                }
            } else {
                if (this.navIndex === 0) {
                    this.tree.scrollTop = 0;
                } else if (r.top + r.height > this.optsRect.top + this.optsRect.height) {
                    this.tree.scrollTop = this.tree.scrollTop + (r.top + r.height - (this.optsRect.top + this.optsRect.height));
                }
                if (this.navIndex === this.tree.childElementCount - 1 && this.requiresPagination) {
                    load.call(this);
                }
            }
            if (prevEl) {
                util.removeClass(prevEl, "active");
            }
            util.addClass(this.items[this.navIndex], "active");
        };
        var addTag = function addTag(item) {
            var that = this, r;
            var docFrag = document.createDocumentFragment();
            var option = this.options[item.idx];
            var data = this.data ? this.data[item.idx] : option;
            var content = this.customSelected ? this.config.renderSelection(data) : option.textContent;
            var tag = util.createElement("li", {
                "class": "selectr-tag",
                html: content
            });
            var btn = util.createElement("button", {
                "class": "selectr-tag-remove",
                type: "button"
            });
            tag.appendChild(btn);
            tag.idx = item.idx;
            tag.tag = option.value;
            this.tags.push(tag);
            if (this.config.sortSelected) {
                var tags = this.tags.slice();
                r = function r(val, arr) {
                    val.replace(/(\d+)|(\D+)/g, function(that, $1, $2) {
                        arr.push([$1 || Infinity, $2 || ""]);
                    });
                }
                ;
                tags.sort(function(a, b) {
                    var x = [], y = [], ac, bc;
                    if (that.config.sortSelected === true) {
                        ac = a.tag;
                        bc = b.tag;
                    } else if (that.config.sortSelected === 'text') {
                        ac = a.textContent;
                        bc = b.textContent;
                    }
                    r(ac, x);
                    r(bc, y);
                    while (x.length && y.length) {
                        var ax = x.shift();
                        var by = y.shift();
                        var nn = ax[0] - by[0] || ax[1].localeCompare(by[1]);
                        if (nn)
                            return nn;
                    }
                    return x.length - y.length;
                });
                util.each(tags, function(i, tg) {
                    docFrag.appendChild(tg);
                });
                this.label.textContent = "";
            } else {
                docFrag.appendChild(tag);
            }
            if (this.config.taggable) {
                this.label.insertBefore(docFrag, this.input.parentNode);
            } else {
                this.label.appendChild(docFrag);
            }
        };
        var removeTag = function removeTag(item) {
            var tag = false;
            util.each(this.tags, function(i, t) {
                if (t.idx === item.idx) {
                    tag = t;
                }
            }, this);
            if (tag) {
                this.label.removeChild(tag);
                this.tags.splice(this.tags.indexOf(tag), 1);
            }
        };
        var load = function load() {
            var tree = this.tree;
            var scrollTop = tree.scrollTop;
            var scrollHeight = tree.scrollHeight;
            var offsetHeight = tree.offsetHeight;
            var atBottom = scrollTop >= scrollHeight - offsetHeight;
            if (atBottom && this.pageIndex < this.pages.length) {
                var f = document.createDocumentFragment();
                util.each(this.pages[this.pageIndex], function(i, item) {
                    appendItem(item, f, this.customOption);
                }, this);
                tree.appendChild(f);
                this.pageIndex++;
                this.emit("selectr.paginate", {
                    items: this.items.length,
                    total: this.data.length,
                    page: this.pageIndex,
                    pages: this.pages.length
                });
            }
        };
        var clearSearch = function clearSearch() {
            if (this.config.searchable || this.config.taggable) {
                this.input.value = null;
                this.searching = false;
                if (this.config.searchable) {
                    util.removeClass(this.inputContainer, "active");
                }
                if (util.hasClass(this.container, "notice")) {
                    util.removeClass(this.container, "notice");
                    util.addClass(this.container, "open");
                    this.input.focus();
                }
                util.each(this.items, function(i, item) {
                    util.removeClass(item, "excluded");
                    if (!this.customOption) {
                        item.textContent = item.textContent + '';
                    }
                }, this);
            }
        };
        var match = function match(query, text) {
            var result = new RegExp(query,"i").exec(text);
            if (result) {
                var start = result.index;
                var end = result.index + result[0].length;
                return {
                    before: text.substring(0, start),
                    match: text.substring(start, end),
                    after: text.substring(end)
                };
            }
            return null;
        };
        var Selectr = function Selectr(el, config) {
            config = config || {};
            if (!el) {
                throw new Error("You must supply either a HTMLSelectElement or a CSS3 selector string.");
            }
            this.el = el;
            if (typeof el === "string") {
                this.el = document.querySelector(el);
            }
            if (this.el === null) {
                throw new Error("The element you passed to Selectr can not be found.");
            }
            if (this.el.nodeName.toLowerCase() !== "select") {
                throw new Error("The element you passed to Selectr is not a HTMLSelectElement.");
            }
            this.render(config);
        };
        Selectr.prototype.render = function(config) {
            if (this.rendered)
                return;
            this.config = util.extend(defaultConfig, config);
            this.originalType = this.el.type;
            this.originalIndex = this.el.tabIndex;
            this.defaultSelected = [];
            this.originalOptionCount = this.el.options.length;
            if (this.config.multiple || this.config.taggable) {
                this.el.multiple = true;
            }
            this.disabled = isset(this.config, "disabled");
            this.opened = false;
            if (this.config.taggable) {
                this.config.searchable = false;
            }
            this.navigating = false;
            this.mobileDevice = false;
            if (/Android|webOS|iPhone|iPad|BlackBerry|Windows Phone|Opera Mini|IEMobile|Mobile/i.test(navigator.userAgent)) {
                this.mobileDevice = true;
            }
            this.customOption = this.config.hasOwnProperty("renderOption") && typeof this.config.renderOption === "function";
            this.customSelected = this.config.hasOwnProperty("renderSelection") && typeof this.config.renderSelection === "function";
            Events.mixin(this);
            build.call(this);
            this.bindEvents();
            this.update();
            this.optsRect = util.rect(this.tree);
            this.rendered = true;
            if (!this.el.multiple) {
                this.el.selectedIndex = this.selectedIndex;
            }
            var that = this;
            setTimeout(function() {
                that.emit("selectr.init");
            }, 20);
        }
        ;
        Selectr.prototype.getSelected = function() {
            var selected = this.el.querySelectorAll('option:checked');
            return selected;
        }
        ;
        Selectr.prototype.getSelectedProperties = function(prop) {
            var selected = this.getSelected();
            var values = [].slice.call(selected).map(function(option) {
                return option[prop];
            }).filter(function(i) {
                return i !== null && i !== undefined;
            });
            return values;
        }
        ;
        Selectr.prototype.bindEvents = function() {
            var that = this;
            this.events = {};
            this.events.dismiss = dismiss.bind(this);
            this.events.navigate = navigate.bind(this);
            this.events.reset = this.reset.bind(this);
            if (this.config.nativeDropdown || this.mobileDevice) {
                this.container.addEventListener("touchstart", function(e) {
                    if (e.changedTouches[0].target === that.el) {
                        that.toggle();
                    }
                });
                if (this.config.nativeDropdown || this.mobileDevice) {
                    this.container.addEventListener("click", function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (e.target === that.el) {
                            that.toggle();
                        }
                    });
                }
                var getChangedOptions = function getChangedOptions(last, current) {
                    var added = []
                      , removed = last.slice(0);
                    var idx;
                    for (var i = 0; i < current.length; i++) {
                        idx = removed.indexOf(current[i]);
                        if (idx > -1)
                            removed.splice(idx, 1);
                        else
                            added.push(current[i]);
                    }
                    return [added, removed];
                };
                this.el.addEventListener("change", function(e) {
                    if (that.el.multiple) {
                        var indexes = that.getSelectedProperties('idx');
                        var changes = getChangedOptions(that.selectedIndexes, indexes);
                        util.each(changes[0], function(i, idx) {
                            that.select(idx);
                        }, that);
                        util.each(changes[1], function(i, idx) {
                            that.deselect(idx);
                        }, that);
                    } else {
                        if (that.el.selectedIndex > -1) {
                            that.select(that.el.selectedIndex);
                        }
                    }
                });
            }
            if (this.config.nativeDropdown) {
                this.container.addEventListener("keydown", function(e) {
                    if (e.key === "Enter" && that.selected === document.activeElement) {
                        that.toggle();
                        setTimeout(function() {
                            that.el.focus();
                        }, 200);
                    }
                });
            }
            this.selected.addEventListener("click", function(e) {
                if (!that.disabled) {
                    that.toggle();
                }
                e.preventDefault();
                e.stopPropagation();
            });
            this.label.addEventListener("click", function(e) {
                if (util.hasClass(e.target, "selectr-tag-remove")) {
                    that.deselect(e.target.parentNode.idx);
                }
            });
            if (this.selectClear) {
                this.selectClear.addEventListener("click", this.clear.bind(this));
            }
            this.tree.addEventListener("mousedown", function(e) {
                e.preventDefault();
            });
            this.tree.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                var item = util.closest(e.target, function(el) {
                    return el && util.hasClass(el, "selectr-option");
                });
                if (item) {
                    if (!util.hasClass(item, "disabled")) {
                        if (util.hasClass(item, "selected")) {
                            if (that.el.multiple || !that.el.multiple && that.config.allowDeselect) {
                                that.deselect(item.idx);
                            }
                        } else {
                            that.select(item.idx);
                        }
                        if (that.opened && !that.el.multiple) {
                            that.close();
                        }
                    }
                }
            });
            this.tree.addEventListener("mouseover", function(e) {
                if (util.hasClass(e.target, "selectr-option")) {
                    if (!util.hasClass(e.target, "disabled")) {
                        util.removeClass(that.items[that.navIndex], "active");
                        util.addClass(e.target, "active");
                        that.navIndex = [].slice.call(that.items).indexOf(e.target);
                    }
                }
            });
            if (this.config.searchable) {
                this.input.addEventListener("focus", function(e) {
                    that.searching = true;
                });
                this.input.addEventListener("blur", function(e) {
                    that.searching = false;
                });
                this.input.addEventListener("keyup", function(e) {
                    that.search();
                    if (!that.config.taggable) {
                        if (this.value.length) {
                            util.addClass(this.parentNode, "active");
                        } else {
                            util.removeClass(this.parentNode, "active");
                        }
                    }
                });
                this.inputClear.addEventListener("click", function(e) {
                    that.input.value = null;
                    clearSearch.call(that);
                    if (!that.tree.childElementCount) {
                        render.call(that);
                    }
                });
            }
            if (this.config.taggable) {
                this.input.addEventListener("keyup", function(e) {
                    that.search();
                    if (that.config.taggable && this.value.length) {
                        var val = this.value.trim();
                        if (e.which === 13 || util.includes(that.tagSeperators, e.key)) {
                            util.each(that.tagSeperators, function(i, k) {
                                val = val.replace(k, '');
                            });
                            var option = that.add({
                                value: val,
                                text: val,
                                selected: true
                            }, true);
                            if (!option) {
                                this.value = '';
                                that.setMessage('That tag is already in use.');
                            } else {
                                that.close();
                                clearSearch.call(that);
                            }
                        }
                    }
                });
            }
            this.update = util.debounce(function() {
                if (that.opened && that.config.closeOnScroll) {
                    that.close();
                }
                if (that.width) {
                    that.container.style.width = that.width;
                }
                that.invert();
            }, 50);
            if (this.requiresPagination) {
                this.paginateItems = util.debounce(function() {
                    load.call(this);
                }, 50);
                this.tree.addEventListener("scroll", this.paginateItems.bind(this));
            }
            document.addEventListener("click", this.events.dismiss);
            window.addEventListener("keydown", this.events.navigate);
            window.addEventListener("resize", this.update);
            window.addEventListener("scroll", this.update);
            if (this.el.form) {
                this.el.form.addEventListener("reset", this.events.reset);
            }
        }
        ;
        Selectr.prototype.setSelected = function(reset) {
            if (!this.config.data && !this.el.multiple && this.el.options.length) {
                if (this.el.selectedIndex === 0) {
                    if (!this.el.options[0].defaultSelected && !this.config.defaultSelected) {
                        this.el.selectedIndex = -1;
                    }
                }
                this.selectedIndex = this.el.selectedIndex;
                if (this.selectedIndex > -1) {
                    this.select(this.selectedIndex);
                }
            }
            if (this.config.multiple && this.originalType === "select-one" && !this.config.data) {
                if (this.el.options[0].selected && !this.el.options[0].defaultSelected) {
                    this.el.options[0].selected = false;
                }
            }
            util.each(this.options, function(i, option) {
                if (option.selected && option.defaultSelected) {
                    this.select(option.idx);
                }
            }, this);
            if (this.config.selectedValue) {
                this.setValue(this.config.selectedValue);
            }
            if (this.config.data) {
                if (!this.el.multiple && this.config.defaultSelected && this.el.selectedIndex < 0) {
                    this.select(0);
                }
                var j = 0;
                util.each(this.config.data, function(i, opt) {
                    if (isset(opt, "children")) {
                        util.each(opt.children, function(x, item) {
                            if (item.hasOwnProperty("selected") && item.selected === true) {
                                this.select(j);
                            }
                            j++;
                        }, this);
                    } else {
                        if (opt.hasOwnProperty("selected") && opt.selected === true) {
                            this.select(j);
                        }
                        j++;
                    }
                }, this);
            }
        }
        ;
        Selectr.prototype.destroy = function() {
            if (!this.rendered)
                return;
            this.emit("selectr.destroy");
            if (this.originalType === 'select-one') {
                this.el.multiple = false;
            }
            if (this.config.data) {
                this.el.textContent = "";
            }
            util.removeClass(this.el, 'selectr-hidden');
            if (this.el.form) {
                util.off(this.el.form, "reset", this.events.reset);
            }
            util.off(document, "click", this.events.dismiss);
            util.off(document, "keydown", this.events.navigate);
            util.off(window, "resize", this.update);
            util.off(window, "scroll", this.update);
            this.container.parentNode.replaceChild(this.el, this.container);
            this.rendered = false;
        }
        ;
        Selectr.prototype.change = function(index) {
            var item = this.items[index]
              , option = this.options[index];
            if (option.disabled) {
                return;
            }
            if (option.selected && util.hasClass(item, "selected")) {
                this.deselect(index);
            } else {
                this.select(index);
            }
            if (this.opened && !this.el.multiple) {
                this.close();
            }
        }
        ;
        Selectr.prototype.select = function(index) {
            var item = this.items[index]
              , options = [].slice.call(this.el.options)
              , option = this.options[index];
            if (this.el.multiple) {
                if (util.includes(this.selectedIndexes, index)) {
                    return false;
                }
                if (this.config.maxSelections && this.tags.length === this.config.maxSelections) {
                    this.setMessage("A maximum of " + this.config.maxSelections + " items can be selected.", true);
                    return false;
                }
                this.selectedValues.push(option.value);
                this.selectedIndexes.push(index);
                addTag.call(this, item);
            } else {
                var data = this.data ? this.data[index] : option;
                this.label.textContent = this.customSelected ? this.config.renderSelection(data) : option.textContent;
                this.selectedValue = option.value;
                this.selectedIndex = index;
                util.each(this.options, function(i, o) {
                    var opt = this.items[i];
                    if (i !== index) {
                        if (opt) {
                            util.removeClass(opt, "selected");
                        }
                        o.selected = false;
                        o.removeAttribute("selected");
                    }
                }, this);
            }
            if (!util.includes(options, option)) {
                this.el.add(option);
            }
            item.setAttribute("aria-selected", true);
            util.addClass(item, "selected");
            util.addClass(this.container, "has-selected");
            option.selected = true;
            option.setAttribute("selected", "");
            this.emit("selectr.change", option);
            this.emit("selectr.select", option);
        }
        ;
        Selectr.prototype.deselect = function(index, force) {
            var item = this.items[index]
              , option = this.options[index];
            if (this.el.multiple) {
                var selIndex = this.selectedIndexes.indexOf(index);
                this.selectedIndexes.splice(selIndex, 1);
                var valIndex = this.selectedValues.indexOf(option.value);
                this.selectedValues.splice(valIndex, 1);
                removeTag.call(this, item);
                if (!this.tags.length) {
                    util.removeClass(this.container, "has-selected");
                }
            } else {
                if (!force && !this.config.clearable && !this.config.allowDeselect) {
                    return false;
                }
                this.label.textContent = "";
                this.selectedValue = null;
                this.el.selectedIndex = this.selectedIndex = -1;
                util.removeClass(this.container, "has-selected");
            }
            this.items[index].setAttribute("aria-selected", false);
            util.removeClass(this.items[index], "selected");
            option.selected = false;
            option.removeAttribute("selected");
            this.emit("selectr.change", null);
            this.emit("selectr.deselect", option);
        }
        ;
        Selectr.prototype.setValue = function(value) {
            var isArray = Array.isArray(value);
            if (!isArray) {
                value = value.toString().trim();
            }
            if (!this.el.multiple && isArray) {
                return false;
            }
            util.each(this.options, function(i, option) {
                if (isArray && util.includes(value.toString(), option.value) || option.value === value) {
                    this.change(option.idx);
                }
            }, this);
        }
        ;
        Selectr.prototype.getValue = function(toObject, toJson) {
            var value;
            if (this.el.multiple) {
                if (toObject) {
                    if (this.selectedIndexes.length) {
                        value = {};
                        value.values = [];
                        util.each(this.selectedIndexes, function(i, index) {
                            var option = this.options[index];
                            value.values[i] = {
                                value: option.value,
                                text: option.textContent
                            };
                        }, this);
                    }
                } else {
                    value = this.selectedValues.slice();
                }
            } else {
                if (toObject) {
                    var option = this.options[this.selectedIndex];
                    value = {
                        value: option.value,
                        text: option.textContent
                    };
                } else {
                    value = this.selectedValue;
                }
            }
            if (toObject && toJson) {
                value = JSON.stringify(value);
            }
            return value;
        }
        ;
        Selectr.prototype.add = function(data, checkDuplicate) {
            if (data) {
                this.data = this.data || [];
                this.items = this.items || [];
                this.options = this.options || [];
                if (Array.isArray(data)) {
                    util.each(data, function(i, obj) {
                        this.add(obj, checkDuplicate);
                    }, this);
                } else if ("[object Object]" === Object.prototype.toString.call(data)) {
                    if (checkDuplicate) {
                        var dupe = false;
                        util.each(this.options, function(i, option) {
                            if (option.value.toLowerCase() === data.value.toLowerCase()) {
                                dupe = true;
                            }
                        });
                        if (dupe) {
                            return false;
                        }
                    }
                    var option = util.createElement('option', data);
                    this.data.push(data);
                    this.options.push(option);
                    option.idx = this.options.length > 0 ? this.options.length - 1 : 0;
                    createItem.call(this, option);
                    if (data.selected) {
                        this.select(option.idx);
                    }
                    return option;
                }
                this.setPlaceholder();
                if (this.config.pagination) {
                    this.paginate();
                }
                return true;
            }
        }
        ;
        Selectr.prototype.remove = function(o) {
            var options = [];
            if (Array.isArray(o)) {
                util.each(o, function(i, opt) {
                    if (util.isInt(opt)) {
                        options.push(this.getOptionByIndex(opt));
                    } else if (typeof o === "string") {
                        options.push(this.getOptionByValue(opt));
                    }
                }, this);
            } else if (util.isInt(o)) {
                options.push(this.getOptionByIndex(o));
            } else if (typeof o === "string") {
                options.push(this.getOptionByValue(o));
            }
            if (options.length) {
                var index;
                util.each(options, function(i, option) {
                    index = option.idx;
                    this.el.remove(option);
                    this.options.splice(index, 1);
                    var parentNode = this.items[index].parentNode;
                    if (parentNode) {
                        parentNode.removeChild(this.items[index]);
                    }
                    this.items.splice(index, 1);
                    util.each(this.options, function(i, opt) {
                        opt.idx = i;
                        this.items[i].idx = i;
                    }, this);
                }, this);
                this.setPlaceholder();
                if (this.config.pagination) {
                    this.paginate();
                }
            }
        }
        ;
        Selectr.prototype.removeAll = function() {
            this.clear(true);
            util.each(this.el.options, function(i, option) {
                this.el.remove(option);
            }, this);
            util.truncate(this.tree);
            this.items = [];
            this.options = [];
            this.data = [];
            this.navIndex = 0;
            if (this.requiresPagination) {
                this.requiresPagination = false;
                this.pageIndex = 1;
                this.pages = [];
            }
            this.setPlaceholder();
        }
        ;
        Selectr.prototype.search = function(string) {
            if (this.navigating)
                return;
            string = string || this.input.value;
            var f = document.createDocumentFragment();
            this.removeMessage();
            util.truncate(this.tree);
            if (string.length > 1) {
                util.each(this.options, function(i, option) {
                    var item = this.items[option.idx];
                    var includes = util.includes(option.textContent.toLowerCase(), string.toLowerCase());
                    if (includes && !option.disabled) {
                        appendItem(item, f, this.customOption);
                        util.removeClass(item, "excluded");
                        if (!this.customOption) {
                            item.textContent = '';
                            var result = match(string, option.textContent);
                            if (result) {
                                item.appendChild(document.createTextNode(result.before));
                                var highlight = document.createElement('span');
                                highlight.className = 'selectr-match';
                                highlight.appendChild(document.createTextNode(result.match));
                                item.appendChild(highlight);
                                item.appendChild(document.createTextNode(result.after));
                            }
                        }
                    } else {
                        util.addClass(item, "excluded");
                    }
                }, this);
                if (!f.childElementCount) {
                    if (!this.config.taggable) {
                        this.setMessage("no results.");
                    }
                } else {
                    var prevEl = this.items[this.navIndex];
                    var firstEl = f.firstElementChild;
                    util.removeClass(prevEl, "active");
                    this.navIndex = firstEl.idx;
                    util.addClass(firstEl, "active");
                }
            } else {
                render.call(this);
            }
            this.tree.appendChild(f);
        }
        ;
        Selectr.prototype.toggle = function() {
            if (!this.disabled) {
                if (this.opened) {
                    this.close();
                } else {
                    this.open();
                }
            }
        }
        ;
        Selectr.prototype.open = function() {
            var that = this;
            if (!this.options.length) {
                return false;
            }
            if (!this.opened) {
                this.emit("selectr.open");
            }
            this.opened = true;
            if (this.mobileDevice || this.config.nativeDropdown) {
                util.addClass(this.container, "native-open");
                if (this.config.data) {
                    util.each(this.options, function(i, option) {
                        this.el.add(option);
                    }, this);
                }
                return;
            }
            util.addClass(this.container, "open");
            render.call(this);
            this.invert();
            this.tree.scrollTop = 0;
            util.removeClass(this.container, "notice");
            this.selected.setAttribute("aria-expanded", true);
            this.tree.setAttribute("aria-hidden", false);
            this.tree.setAttribute("aria-expanded", true);
            if (this.config.searchable && !this.config.taggable) {
                setTimeout(function() {
                    that.input.focus();
                    that.input.tabIndex = 0;
                }, 10);
            }
        }
        ;
        Selectr.prototype.close = function() {
            if (this.opened) {
                this.emit("selectr.close");
            }
            this.opened = false;
            if (this.mobileDevice || this.config.nativeDropdown) {
                util.removeClass(this.container, "native-open");
                return;
            }
            var notice = util.hasClass(this.container, "notice");
            if (this.config.searchable && !notice) {
                this.input.blur();
                this.input.tabIndex = -1;
                this.searching = false;
            }
            if (notice) {
                util.removeClass(this.container, "notice");
                this.notice.textContent = "";
            }
            util.removeClass(this.container, "open");
            util.removeClass(this.container, "native-open");
            this.selected.setAttribute("aria-expanded", false);
            this.tree.setAttribute("aria-hidden", true);
            this.tree.setAttribute("aria-expanded", false);
            util.truncate(this.tree);
            clearSearch.call(this);
        }
        ;
        Selectr.prototype.enable = function() {
            this.disabled = false;
            this.el.disabled = false;
            this.selected.tabIndex = this.originalIndex;
            if (this.el.multiple) {
                util.each(this.tags, function(i, t) {
                    t.lastElementChild.tabIndex = 0;
                });
            }
            util.removeClass(this.container, "selectr-disabled");
        }
        ;
        Selectr.prototype.disable = function(container) {
            if (!container) {
                this.el.disabled = true;
            }
            this.selected.tabIndex = -1;
            if (this.el.multiple) {
                util.each(this.tags, function(i, t) {
                    t.lastElementChild.tabIndex = -1;
                });
            }
            this.disabled = true;
            util.addClass(this.container, "selectr-disabled");
        }
        ;
        Selectr.prototype.reset = function() {
            if (!this.disabled) {
                this.clear();
                this.setSelected(true);
                util.each(this.defaultSelected, function(i, idx) {
                    this.select(idx);
                }, this);
                this.emit("selectr.reset");
            }
        }
        ;
        Selectr.prototype.clear = function(force) {
            if (this.el.multiple) {
                if (this.selectedIndexes.length) {
                    var indexes = this.selectedIndexes.slice();
                    util.each(indexes, function(i, idx) {
                        this.deselect(idx);
                    }, this);
                }
            } else {
                if (this.selectedIndex > -1) {
                    this.deselect(this.selectedIndex, force);
                }
            }
            this.emit("selectr.clear");
        }
        ;
        Selectr.prototype.serialise = function(toJson) {
            var data = [];
            util.each(this.options, function(i, option) {
                var obj = {
                    value: option.value,
                    text: option.textContent
                };
                if (option.selected) {
                    obj.selected = true;
                }
                if (option.disabled) {
                    obj.disabled = true;
                }
                data[i] = obj;
            });
            return toJson ? JSON.stringify(data) : data;
        }
        ;
        Selectr.prototype.serialize = function(toJson) {
            return this.serialise(toJson);
        }
        ;
        Selectr.prototype.setPlaceholder = function(placeholder) {
            placeholder = placeholder || this.config.placeholder || this.el.getAttribute("placeholder");
            if (!this.options.length) {
                placeholder = "No options available";
            }
            this.placeEl.textContent = placeholder;
        }
        ;
        Selectr.prototype.paginate = function() {
            if (this.items.length) {
                var that = this;
                this.pages = this.items.map(function(v, i) {
                    return i % that.config.pagination === 0 ? that.items.slice(i, i + that.config.pagination) : null;
                }).filter(function(pages) {
                    return pages;
                });
                return this.pages;
            }
        }
        ;
        Selectr.prototype.setMessage = function(message, close) {
            if (close) {
                this.close();
            }
            util.addClass(this.container, "notice");
            this.notice.textContent = message;
        }
        ;
        Selectr.prototype.removeMessage = function() {
            util.removeClass(this.container, "notice");
            this.notice.textContent = "";
        }
        ;
        Selectr.prototype.invert = function() {
            var rt = util.rect(this.selected)
              , oh = this.tree.parentNode.offsetHeight
              , wh = window.innerHeight
              , doInvert = rt.top + rt.height + oh > wh;
            if (doInvert) {
                util.addClass(this.container, "inverted");
                this.isInverted = true;
            } else {
                util.removeClass(this.container, "inverted");
                this.isInverted = false;
            }
            this.optsRect = util.rect(this.tree);
        }
        ;
        Selectr.prototype.getOptionByIndex = function(index) {
            return this.options[index];
        }
        ;
        Selectr.prototype.getOptionByValue = function(value) {
            var option = false;
            for (var i = 0, l = this.options.length; i < l; i++) {
                if (this.options[i].value.trim() === value.toString().trim()) {
                    option = this.options[i];
                    break;
                }
            }
            return option;
        }
        ;
        module.exports = Selectr;
    }
    ), (function(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        __webpack_require__.d(__webpack_exports__, "a", function() {
            return createAbsoluteAnchor;
        });
        var _util__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(0);
        function createAbsoluteAnchor(anchor, parent, onDestroy) {
            var destroyOnMouseOut = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
            var root = getRootNode(anchor);
            var eventListeners = {};
            var anchorRect = anchor.getBoundingClientRect();
            var parentRect = parent.getBoundingClientRect();
            var absoluteAnchor = document.createElement('div');
            absoluteAnchor.className = 'jsoneditor-anchor';
            absoluteAnchor.style.position = 'absolute';
            absoluteAnchor.style.left = anchorRect.left - parentRect.left + 'px';
            absoluteAnchor.style.top = anchorRect.top - parentRect.top + 'px';
            absoluteAnchor.style.width = anchorRect.width - 2 + 'px';
            absoluteAnchor.style.height = anchorRect.height - 2 + 'px';
            absoluteAnchor.style.boxSizing = 'border-box';
            parent.appendChild(absoluteAnchor);
            function destroy() {
                if (absoluteAnchor && absoluteAnchor.parentNode) {
                    absoluteAnchor.parentNode.removeChild(absoluteAnchor);
                    for (var name in eventListeners) {
                        if (hasOwnProperty(eventListeners, name)) {
                            var fn = eventListeners[name];
                            if (fn) {
                                Object(_util__WEBPACK_IMPORTED_MODULE_0__["removeEventListener"])(root, name, fn);
                            }
                            delete eventListeners[name];
                        }
                    }
                    if (typeof onDestroy === 'function') {
                        onDestroy(anchor);
                    }
                }
            }
            function isOutside(target) {
                return target !== absoluteAnchor && !Object(_util__WEBPACK_IMPORTED_MODULE_0__["isChildOf"])(target, absoluteAnchor);
            }
            function destroyIfOutside(event) {
                if (isOutside(event.target)) {
                    destroy();
                }
            }
            eventListeners.mousedown = Object(_util__WEBPACK_IMPORTED_MODULE_0__["addEventListener"])(root, 'mousedown', destroyIfOutside);
            eventListeners.mousewheel = Object(_util__WEBPACK_IMPORTED_MODULE_0__["addEventListener"])(root, 'mousewheel', destroyIfOutside);
            if (destroyOnMouseOut) {
                var destroyTimer = null;
                absoluteAnchor.onmouseover = function() {
                    clearTimeout(destroyTimer);
                    destroyTimer = null;
                }
                ;
                absoluteAnchor.onmouseout = function() {
                    if (!destroyTimer) {
                        destroyTimer = setTimeout(destroy, 200);
                    }
                }
                ;
            }
            absoluteAnchor.destroy = destroy;
            return absoluteAnchor;
        }
        function getRootNode(node) {
            return typeof node.getRootNode === 'function' ? node.getRootNode() : window;
        }
        function hasOwnProperty(object, key) {
            return Object.prototype.hasOwnProperty.call(object, key);
        }
    }
    ), (function(module, exports) {
        module.exports = function naturalSort(a, b) {
            "use strict";
            var re = /(^([+\-]?(?:0|[1-9]\d*)(?:\.\d*)?(?:[eE][+\-]?\d+)?)?$|^0x[0-9a-f]+$|\d+)/gi, sre = /(^[ ]*|[ ]*$)/g, dre = /(^([\w ]+,?[\w ]+)?[\w ]+,?[\w ]+\d+:\d+(:\d+)?[\w ]?|^\d{1,4}[\/\-]\d{1,4}[\/\-]\d{1,4}|^\w+, \w+ \d+, \d{4})/, hre = /^0x[0-9a-f]+$/i, ore = /^0/, i = function(s) {
                return naturalSort.insensitive && ('' + s).toLowerCase() || '' + s;
            }, x = i(a).replace(sre, '') || '', y = i(b).replace(sre, '') || '', xN = x.replace(re, '\0$1\0').replace(/\0$/, '').replace(/^\0/, '').split('\0'), yN = y.replace(re, '\0$1\0').replace(/\0$/, '').replace(/^\0/, '').split('\0'), xD = parseInt(x.match(hre), 16) || (xN.length !== 1 && x.match(dre) && Date.parse(x)), yD = parseInt(y.match(hre), 16) || xD && y.match(dre) && Date.parse(y) || null, oFxNcL, oFyNcL;
            if (yD) {
                if (xD < yD) {
                    return -1;
                } else if (xD > yD) {
                    return 1;
                }
            }
            for (var cLoc = 0, numS = Math.max(xN.length, yN.length); cLoc < numS; cLoc++) {
                oFxNcL = !(xN[cLoc] || '').match(ore) && parseFloat(xN[cLoc]) || xN[cLoc] || 0;
                oFyNcL = !(yN[cLoc] || '').match(ore) && parseFloat(yN[cLoc]) || yN[cLoc] || 0;
                if (isNaN(oFxNcL) !== isNaN(oFyNcL)) {
                    return (isNaN(oFxNcL)) ? 1 : -1;
                } else if (typeof oFxNcL !== typeof oFyNcL) {
                    oFxNcL += '';
                    oFyNcL += '';
                }
                if (oFxNcL < oFyNcL) {
                    return -1;
                }
                if (oFxNcL > oFyNcL) {
                    return 1;
                }
            }
            return 0;
        }
        ;
    }
    ), (function(module, exports, __webpack_require__) {
        var VanillaPicker;
        if (window.Picker) {
            VanillaPicker = window.Picker;
        } else {
            try {
                VanillaPicker = __webpack_require__(!(function webpackMissingModule() {
                    var e = new Error("Cannot find module 'vanilla-picker'");
                    e.code = 'MODULE_NOT_FOUND';
                    throw e;
                }()));
            } catch (err) {}
        }
        module.exports = VanillaPicker;
    }
    ), (function(module, exports, __webpack_require__) {
        var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;
        (function(root, factory) {
            "use strict";
            if (true) {
                !(__WEBPACK_AMD_DEFINE_ARRAY__ = [],
                __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
                __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
                __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
            } else {}
        }(this, function() {
            "use strict";
            function isNode(value) {
                if (typeof Node === "object") {
                    return value instanceof Node;
                } else {
                    return value && typeof value === "object" && typeof value.nodeType === "number";
                }
            }
            function isString(value) {
                return typeof value === "string";
            }
            function observable() {
                var callbacks = [];
                return {
                    watch: callbacks.push.bind(callbacks),
                    trigger: function(context, detail) {
                        var unprevented = true;
                        var event = {
                            detail: detail,
                            preventDefault: function preventDefault() {
                                unprevented = false;
                            }
                        };
                        for (var i = 0; i < callbacks.length; i++) {
                            callbacks[i](context, event);
                        }
                        return unprevented;
                    }
                };
            }
            function isHidden(elem) {
                return window.getComputedStyle(elem).display === 'none';
            }
            function Elem(elem) {
                this.elem = elem;
            }
            Elem.make = function(parent, tag) {
                if (typeof parent === "string") {
                    parent = document.querySelector(parent);
                }
                var elem = document.createElement(tag || 'div');
                (parent || document.body).appendChild(elem);
                return new Elem(elem);
            }
            ;
            Elem.prototype = {
                child: function(tag) {
                    return Elem.make(this.elem, tag);
                },
                stylize: function(styles) {
                    styles = styles || {};
                    if (typeof styles.opacity !== "undefined") {
                        styles.filter = "alpha(opacity=" + (styles.opacity * 100) + ")";
                    }
                    for (var prop in styles) {
                        if (styles.hasOwnProperty(prop)) {
                            this.elem.style[prop] = styles[prop];
                        }
                    }
                    return this;
                },
                clazz: function(clazz) {
                    this.elem.className += " " + clazz;
                    return this;
                },
                html: function(content) {
                    if (isNode(content)) {
                        this.elem.appendChild(content);
                    } else {
                        this.elem.innerHTML = content;
                    }
                    return this;
                },
                onClick: function(callback) {
                    this.elem.addEventListener('click', callback);
                    return this;
                },
                destroy: function() {
                    this.elem.parentNode.removeChild(this.elem);
                },
                hide: function() {
                    this.elem.style.display = "none";
                },
                show: function() {
                    this.elem.style.display = "block";
                },
                attr: function(name, value) {
                    if (value !== undefined) {
                        this.elem.setAttribute(name, value);
                    }
                    return this;
                },
                anyAncestor: function(predicate) {
                    var elem = this.elem;
                    while (elem) {
                        if (predicate(new Elem(elem))) {
                            return true;
                        } else {
                            elem = elem.parentNode;
                        }
                    }
                    return false;
                },
                isVisible: function() {
                    return !isHidden(this.elem);
                }
            };
            function buildOverlay(getOption, close) {
                return Elem.make(getOption("parent")).clazz("pico-overlay").clazz(getOption("overlayClass", "")).stylize({
                    display: "none",
                    position: "fixed",
                    top: "0px",
                    left: "0px",
                    height: "100%",
                    width: "100%",
                    zIndex: 10000
                }).stylize(getOption('overlayStyles', {
                    opacity: 0.5,
                    background: "#000"
                })).onClick(function() {
                    if (getOption('overlayClose', true)) {
                        close();
                    }
                });
            }
            var autoinc = 1;
            function buildModal(getOption, close) {
                var width = getOption('width', 'auto');
                if (typeof width === "number") {
                    width = "" + width + "px";
                }
                var id = getOption("modalId", "pico-" + autoinc++);
                var elem = Elem.make(getOption("parent")).clazz("pico-content").clazz(getOption("modalClass", "")).stylize({
                    display: 'none',
                    position: 'fixed',
                    zIndex: 10001,
                    left: "50%",
                    top: "38.1966%",
                    maxHeight: '90%',
                    boxSizing: 'border-box',
                    width: width,
                    '-ms-transform': 'translate(-50%,-38.1966%)',
                    '-moz-transform': 'translate(-50%,-38.1966%)',
                    '-webkit-transform': 'translate(-50%,-38.1966%)',
                    '-o-transform': 'translate(-50%,-38.1966%)',
                    transform: 'translate(-50%,-38.1966%)'
                }).stylize(getOption('modalStyles', {
                    overflow: 'auto',
                    backgroundColor: "white",
                    padding: "20px",
                    borderRadius: "5px"
                })).html(getOption('content')).attr("id", id).attr("role", "dialog").attr("aria-labelledby", getOption("ariaLabelledBy")).attr("aria-describedby", getOption("ariaDescribedBy", id)).onClick(function(event) {
                    var isCloseClick = new Elem(event.target).anyAncestor(function(elem) {
                        return /\bpico-close\b/.test(elem.elem.className);
                    });
                    if (isCloseClick) {
                        close();
                    }
                });
                return elem;
            }
            function buildClose(elem, getOption) {
                if (getOption('closeButton', true)) {
                    return elem.child('button').html(getOption('closeHtml', "&#xD7;")).clazz("pico-close").clazz(getOption("closeClass", "")).stylize(getOption('closeStyles', {
                        borderRadius: "2px",
                        border: 0,
                        padding: 0,
                        cursor: "pointer",
                        height: "15px",
                        width: "15px",
                        position: "absolute",
                        top: "5px",
                        right: "5px",
                        fontSize: "16px",
                        textAlign: "center",
                        lineHeight: "15px",
                        background: "#CCC"
                    })).attr("aria-label", getOption("close-label", "Close"));
                }
            }
            function buildElemAccessor(builder) {
                return function() {
                    return builder().elem;
                }
                ;
            }
            var escapeKey = observable();
            var tabKey = observable();
            document.documentElement.addEventListener('keydown', function onKeyPress(event) {
                var keycode = event.which || event.keyCode;
                if (keycode === 27) {
                    escapeKey.trigger();
                } else if (keycode === 9) {
                    tabKey.trigger(event);
                }
            });
            function manageFocus(iface, isEnabled) {
                function matches(elem, selector) {
                    var fn = elem.msMatchesSelector || elem.webkitMatchesSelector || elem.matches;
                    return fn.call(elem, selector);
                }
                function canFocus(elem) {
                    if (isHidden(elem) || matches(elem, ":disabled") || elem.hasAttribute("contenteditable")) {
                        return false;
                    } else {
                        return elem.hasAttribute("tabindex") || matches(elem, "input,select,textarea,button,a[href],area[href],iframe");
                    }
                }
                function firstFocusable(elem) {
                    var items = elem.getElementsByTagName("*");
                    for (var i = 0; i < items.length; i++) {
                        if (canFocus(items[i])) {
                            return items[i];
                        }
                    }
                }
                function lastFocusable(elem) {
                    var items = elem.getElementsByTagName("*");
                    for (var i = items.length; i--; ) {
                        if (canFocus(items[i])) {
                            return items[i];
                        }
                    }
                }
                var focused;
                iface.beforeShow(function getActiveFocus() {
                    focused = document.activeElement;
                });
                iface.afterShow(function focusModal() {
                    if (isEnabled()) {
                        var focusable = firstFocusable(iface.modalElem());
                        if (focusable) {
                            focusable.focus();
                        }
                    }
                });
                iface.afterClose(function returnFocus() {
                    if (isEnabled() && focused) {
                        focused.focus();
                    }
                    focused = null;
                });
                tabKey.watch(function tabKeyPress(event) {
                    if (isEnabled() && iface.isVisible()) {
                        var first = firstFocusable(iface.modalElem());
                        var last = lastFocusable(iface.modalElem());
                        var from = event.shiftKey ? first : last;
                        if (from === document.activeElement) {
                            (event.shiftKey ? last : first).focus();
                            event.preventDefault();
                        }
                    }
                });
            }
            function manageBodyOverflow(iface, isEnabled) {
                var origOverflow;
                var body = new Elem(document.body);
                iface.beforeShow(function() {
                    origOverflow = body.elem.style.overflow;
                    if (isEnabled()) {
                        body.stylize({
                            overflow: "hidden"
                        });
                    }
                });
                iface.afterClose(function() {
                    body.stylize({
                        overflow: origOverflow
                    });
                });
            }
            return function picoModal(options) {
                if (isString(options) || isNode(options)) {
                    options = {
                        content: options
                    };
                }
                var afterCreateEvent = observable();
                var beforeShowEvent = observable();
                var afterShowEvent = observable();
                var beforeCloseEvent = observable();
                var afterCloseEvent = observable();
                function getOption(opt, defaultValue) {
                    var value = options[opt];
                    if (typeof value === "function") {
                        value = value(defaultValue);
                    }
                    return value === undefined ? defaultValue : value;
                }
                var modalElem = build.bind(window, 'modal');
                var shadowElem = build.bind(window, 'overlay');
                var closeElem = build.bind(window, 'close');
                var iface;
                function forceClose(detail) {
                    shadowElem().hide();
                    modalElem().hide();
                    afterCloseEvent.trigger(iface, detail);
                }
                function close(detail) {
                    if (beforeCloseEvent.trigger(iface, detail)) {
                        forceClose(detail);
                    }
                }
                function returnIface(callback) {
                    return function() {
                        callback.apply(this, arguments);
                        return iface;
                    }
                    ;
                }
                var built;
                function build(name, detail) {
                    if (!built) {
                        var modal = buildModal(getOption, close);
                        built = {
                            modal: modal,
                            overlay: buildOverlay(getOption, close),
                            close: buildClose(modal, getOption)
                        };
                        afterCreateEvent.trigger(iface, detail);
                    }
                    return built[name];
                }
                iface = {
                    modalElem: buildElemAccessor(modalElem),
                    closeElem: buildElemAccessor(closeElem),
                    overlayElem: buildElemAccessor(shadowElem),
                    buildDom: returnIface(build.bind(null, null)),
                    isVisible: function() {
                        return !!(built && modalElem && modalElem().isVisible());
                    },
                    show: function(detail) {
                        if (beforeShowEvent.trigger(iface, detail)) {
                            shadowElem().show();
                            closeElem();
                            modalElem().show();
                            afterShowEvent.trigger(iface, detail);
                        }
                        return this;
                    },
                    close: returnIface(close),
                    forceClose: returnIface(forceClose),
                    destroy: function() {
                        modalElem().destroy();
                        shadowElem().destroy();
                        shadowElem = modalElem = closeElem = undefined;
                    },
                    options: function(opts) {
                        Object.keys(opts).map(function(key) {
                            options[key] = opts[key];
                        });
                    },
                    afterCreate: returnIface(afterCreateEvent.watch),
                    beforeShow: returnIface(beforeShowEvent.watch),
                    afterShow: returnIface(afterShowEvent.watch),
                    beforeClose: returnIface(beforeCloseEvent.watch),
                    afterClose: returnIface(afterCloseEvent.watch)
                };
                manageFocus(iface, getOption.bind(null, "focus", true));
                manageBodyOverflow(iface, getOption.bind(null, "bodyOverflow", true));
                escapeKey.watch(function escapeKeyPress() {
                    if (getOption("escCloses", true) && iface.isVisible()) {
                        iface.close();
                    }
                });
                return iface;
            }
            ;
        }));
    }
    ), (function(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        __webpack_require__.d(__webpack_exports__, "a", function() {
            return ErrorTable;
        });
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value"in descriptor)
                    descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps)
                _defineProperties(Constructor.prototype, protoProps);
            if (staticProps)
                _defineProperties(Constructor, staticProps);
            return Constructor;
        }
        var ErrorTable = function() {
            function ErrorTable(config) {
                _classCallCheck(this, ErrorTable);
                this.errorTableVisible = config.errorTableVisible;
                this.onToggleVisibility = config.onToggleVisibility;
                this.onFocusLine = config.onFocusLine || function() {}
                ;
                this.onChangeHeight = config.onChangeHeight;
                this.dom = {};
                var validationErrorsContainer = document.createElement('div');
                validationErrorsContainer.className = 'jsoneditor-validation-errors-container';
                this.dom.validationErrorsContainer = validationErrorsContainer;
                var additionalErrorsIndication = document.createElement('div');
                additionalErrorsIndication.style.display = 'none';
                additionalErrorsIndication.className = 'jsoneditor-additional-errors fadein';
                additionalErrorsIndication.textContent = "Scroll for more \u25BF";
                this.dom.additionalErrorsIndication = additionalErrorsIndication;
                validationErrorsContainer.appendChild(additionalErrorsIndication);
                var validationErrorIcon = document.createElement('span');
                validationErrorIcon.className = 'jsoneditor-validation-error-icon';
                validationErrorIcon.style.display = 'none';
                this.dom.validationErrorIcon = validationErrorIcon;
                var validationErrorCount = document.createElement('span');
                validationErrorCount.className = 'jsoneditor-validation-error-count';
                validationErrorCount.style.display = 'none';
                this.dom.validationErrorCount = validationErrorCount;
                this.dom.parseErrorIndication = document.createElement('span');
                this.dom.parseErrorIndication.className = 'jsoneditor-parse-error-icon';
                this.dom.parseErrorIndication.style.display = 'none';
            }
            _createClass(ErrorTable, [{
                key: "getErrorTable",
                value: function getErrorTable() {
                    return this.dom.validationErrorsContainer;
                }
            }, {
                key: "getErrorCounter",
                value: function getErrorCounter() {
                    return this.dom.validationErrorCount;
                }
            }, {
                key: "getWarningIcon",
                value: function getWarningIcon() {
                    return this.dom.validationErrorIcon;
                }
            }, {
                key: "getErrorIcon",
                value: function getErrorIcon() {
                    return this.dom.parseErrorIndication;
                }
            }, {
                key: "toggleTableVisibility",
                value: function toggleTableVisibility() {
                    this.errorTableVisible = !this.errorTableVisible;
                    this.onToggleVisibility(this.errorTableVisible);
                }
            }, {
                key: "setErrors",
                value: function setErrors(errors, errorLocations) {
                    var _this = this;
                    if (this.dom.validationErrors) {
                        this.dom.validationErrors.parentNode.removeChild(this.dom.validationErrors);
                        this.dom.validationErrors = null;
                        this.dom.additionalErrorsIndication.style.display = 'none';
                    }
                    if (this.errorTableVisible && errors.length > 0) {
                        return;
                        var validationErrors = document.createElement('div');
                        validationErrors.className = 'jsoneditor-validation-errors';
                        var table = document.createElement('table');
                        table.className = 'jsoneditor-text-errors';
                        validationErrors.appendChild(table);
                        var tbody = document.createElement('tbody');
                        table.appendChild(tbody);
                        errors.forEach(function(error) {
                            var line;
                            if (!isNaN(error.line)) {
                                line = error.line;
                            } else if (error.dataPath) {
                                var errLoc = errorLocations.find(function(loc) {
                                    return loc.path === error.dataPath;
                                });
                                if (errLoc) {
                                    line = errLoc.line + 1;
                                }
                            }
                            var trEl = document.createElement('tr');
                            trEl.className = !isNaN(line) ? 'jump-to-line' : '';
                            if (error.type === 'error') {
                                trEl.className += ' parse-error';
                            } else {
                                trEl.className += ' validation-error';
                            }
                            var td1 = document.createElement('td');
                            var button = document.createElement('button');
                            button.className = 'jsoneditor-schema-error';
                            td1.appendChild(button);
                            trEl.appendChild(td1);
                            var td2 = document.createElement('td');
                            td2.style = 'white-space: nowrap;';
                            td2.textContent = !isNaN(line) ? 'Ln ' + line : '';
                            trEl.appendChild(td2);
                            if (typeof error === 'string') {
                                var td34 = document.createElement('td');
                                td34.colSpan = 2;
                                var pre = document.createElement('pre');
                                pre.appendChild(document.createTextNode(error));
                                td34.appendChild(pre);
                                trEl.appendChild(td34);
                            } else {
                                var td3 = document.createElement('td');
                                td3.appendChild(document.createTextNode(error.dataPath || ''));
                                trEl.appendChild(td3);
                                var td4 = document.createElement('td');
                                var _pre = document.createElement('pre');
                                _pre.appendChild(document.createTextNode(error.message));
                                td4.appendChild(_pre);
                                trEl.appendChild(td4);
                            }
                            trEl.onclick = function() {
                                _this.onFocusLine(line);
                            }
                            ;
                            tbody.appendChild(trEl);
                        });
                        this.dom.validationErrors = validationErrors;
                        this.dom.validationErrorsContainer.appendChild(validationErrors);
                        this.dom.additionalErrorsIndication.title = errors.length + ' errors total';
                        if (this.dom.validationErrorsContainer.clientHeight < this.dom.validationErrorsContainer.scrollHeight) {
                            this.dom.additionalErrorsIndication.style.display = 'block';
                            this.dom.validationErrorsContainer.onscroll = function() {
                                _this.dom.additionalErrorsIndication.style.display = _this.dom.validationErrorsContainer.clientHeight > 0 && _this.dom.validationErrorsContainer.scrollTop === 0 ? 'block' : 'none';
                            }
                            ;
                        } else {
                            this.dom.validationErrorsContainer.onscroll = undefined;
                        }
                        var height = this.dom.validationErrorsContainer.clientHeight + (this.dom.statusBar ? this.dom.statusBar.clientHeight : 0);
                        this.onChangeHeight(height);
                    } else {
                        this.onChangeHeight(0);
                    }
                    var validationErrorsCount = errors.filter(function(error) {
                        return error.type !== 'error';
                    }).length;
                    if (validationErrorsCount > 0) {
                        this.dom.validationErrorCount.style.display = 'inline';
                        this.dom.validationErrorCount.innerText = validationErrorsCount;
                        this.dom.validationErrorCount.onclick = this.toggleTableVisibility.bind(this);
                        this.dom.validationErrorIcon.style.display = 'inline';
                        this.dom.validationErrorIcon.title = validationErrorsCount + ' schema validation error(s) found';
                        this.dom.validationErrorIcon.onclick = this.toggleTableVisibility.bind(this);
                    } else {
                        this.dom.validationErrorCount.style.display = 'none';
                        this.dom.validationErrorIcon.style.display = 'none';
                    }
                    var hasParseErrors = errors.some(function(error) {
                        return error.type === 'error';
                    });
                    if (hasParseErrors) {
                        var line = errors[0].line;
                        this.dom.parseErrorIndication.style.display = 'block';
                        this.dom.parseErrorIndication.title = !isNaN(line) ? 'parse error on line ' + line : 'parse error - check that the json is valid';
                        this.dom.parseErrorIndication.onclick = this.toggleTableVisibility.bind(this);
                    } else {
                        this.dom.parseErrorIndication.style.display = 'none';
                    }
                }
            }]);
            return ErrorTable;
        }();
    }
    ), (function(module, exports, __webpack_require__) {
        var ace;
        if (window.ace) {
            ace = window.ace;
        } else {
            try {
                ace = __webpack_require__(!(function webpackMissingModule() {
                    var e = new Error("Cannot find module 'ace-builds/src-noconflict/ace'");
                    e.code = 'MODULE_NOT_FOUND';
                    throw e;
                }()));
                __webpack_require__(!(function webpackMissingModule() {
                    var e = new Error("Cannot find module 'ace-builds/src-noconflict/mode-json'");
                    e.code = 'MODULE_NOT_FOUND';
                    throw e;
                }()));
                __webpack_require__(!(function webpackMissingModule() {
                    var e = new Error("Cannot find module 'ace-builds/src-noconflict/ext-searchbox'");
                    e.code = 'MODULE_NOT_FOUND';
                    throw e;
                }()));
                var jsonWorkerDataUrl = __webpack_require__(!(function webpackMissingModule() {
                    var e = new Error("Cannot find module '../generated/worker-json-data-url'");
                    e.code = 'MODULE_NOT_FOUND';
                    throw e;
                }()));
                ace.config.setModuleUrl('ace/mode/json_worker', jsonWorkerDataUrl);
            } catch (err) {}
        }
        module.exports = ace;
    }
    ), (function(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, "textModeMixins", function() {
            return textModeMixins;
        });
        var simpleJsonRepair_min = __webpack_require__(7);
        var simpleJsonRepair_min_default = __webpack_require__.n(simpleJsonRepair_min);
        var ace = __webpack_require__(16);
        var ace_default = __webpack_require__.n(ace);
        var constants = __webpack_require__(2);
        var ErrorTable = __webpack_require__(15);
        var FocusTracker = __webpack_require__(8);
        var i18n = __webpack_require__(1);
        var jmespathQuery = __webpack_require__(4);
        var ModeSwitcher = __webpack_require__(9);
        var showSortModal = __webpack_require__(5);
        var showTransformModal = __webpack_require__(6);
        var tryRequireThemeJsonEditor = __webpack_require__(22);
        var util = __webpack_require__(0);
        function validateCustom(json, onValidate) {
            if (!onValidate) {
                return Promise.resolve([]);
            }
            try {
                var customValidateResults = onValidate(json);
                var resultPromise = Object(util["isPromise"])(customValidateResults) ? customValidateResults : Promise.resolve(customValidateResults);
                return resultPromise.then(function(customValidationPathErrors) {
                    if (Array.isArray(customValidationPathErrors)) {
                        return customValidationPathErrors.filter(function(error) {
                            var valid = Object(util["isValidValidationError"])(error);
                            if (!valid) {
                                console.warn('Ignoring a custom validation error with invalid structure. ' + 'Expected structure: {path: [...], message: "..."}. ' + 'Actual error:', error);
                            }
                            return valid;
                        }).map(function(error) {
                            return ({
                                dataPath: Object(util["stringifyPath"])(error.path),
                                message: error.message,
                                type: 'customValidation'
                            });
                        });
                    } else {
                        return [];
                    }
                });
            } catch (err) {
                return Promise.reject(err);
            }
        }
        function _typeof(obj) {
            "@babel/helpers - typeof";
            if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
                _typeof = function _typeof(obj) {
                    return typeof obj;
                }
                ;
            } else {
                _typeof = function _typeof(obj) {
                    return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
                }
                ;
            }
            return _typeof(obj);
        }
        var textmode = {};
        var DEFAULT_THEME = 'ace/theme/jsoneditor';
        textmode.create = function(container) {
            var _this = this;
            var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
            if (typeof options.statusBar === 'undefined') {
                options.statusBar = true;
            }
            options.mainMenuBar = options.mainMenuBar !== false;
            options.enableSort = options.enableSort !== false;
            options.enableTransform = options.enableTransform !== false;
            options.createQuery = options.createQuery || jmespathQuery["a"];
            options.executeQuery = options.executeQuery || jmespathQuery["b"];
            this.options = options;
            if (typeof options.indentation === 'number') {
                this.indentation = Number(options.indentation);
            } else {
                this.indentation = 2;
            }
            Object(i18n["b"])(this.options.languages);
            Object(i18n["a"])(this.options.language);
            var _ace = options.ace ? options.ace : ace_default.a;
            this.mode = options.mode === 'code' ? 'code' : 'text';
            if (this.mode === 'code') {
                if (typeof _ace === 'undefined') {
                    this.mode = 'text';
                    console.warn('Failed to load Ace editor, falling back to plain text mode. Please use a JSONEditor bundle including Ace, or pass Ace as via the configuration option `ace`.');
                }
            }
            this.theme = options.theme || DEFAULT_THEME;
            if (this.theme === DEFAULT_THEME && _ace) {
                Object(tryRequireThemeJsonEditor["tryRequireThemeJsonEditor"])();
            }
            if (options.onTextSelectionChange) {
                this.onTextSelectionChange(options.onTextSelectionChange);
            }
            var me = this;
            this.container = container;
            this.dom = {};
            this.aceEditor = undefined;
            this.textarea = undefined;
            this.validateSchema = null;
            this.annotations = [];
            this.lastSchemaErrors = undefined;
            this._debouncedValidate = Object(util["debounce"])(this.validate.bind(this), this.DEBOUNCE_INTERVAL);
            this.width = container.clientWidth;
            this.height = container.clientHeight;
            this.frame = document.createElement('div');
            this.frame.className = 'jsoneditor jsoneditor-mode-' + this.options.mode;
            this.frame.onclick = function(event) {
                event.preventDefault();
            }
            ;
            this.frame.onkeydown = function(event) {
                me._onKeyDown(event);
            }
            ;
            var focusTrackerConfig = {
                target: this.frame,
                onFocus: this.options.onFocus || null,
                onBlur: this.options.onBlur || null
            };
            this.frameFocusTracker = new FocusTracker["a"](focusTrackerConfig);
            this.content = document.createElement('div');
            this.content.className = 'jsoneditor-outer';
            if (this.options.mainMenuBar) {
                Object(util["addClassName"])(this.content, 'has-main-menu-bar');
                this.menu = document.createElement('div');
                this.menu.className = 'jsoneditor-menu';
                this.frame.appendChild(this.menu);
                var buttonFormat = document.createElement('button');
                buttonFormat.type = 'button';
                buttonFormat.className = 'jsoneditor-format';
                buttonFormat.title = Object(i18n["c"])('formatTitle');
                this.menu.appendChild(buttonFormat);
                buttonFormat.onclick = function() {
                    try {
                        me.format();
                        me._onChange();
                    } catch (err) {
                        me._onError(err);
                    }
                }
                ;
                var buttonCompact = document.createElement('button');
                buttonCompact.type = 'button';
                buttonCompact.className = 'jsoneditor-compact';
                buttonCompact.title = Object(i18n["c"])('compactTitle');
                this.menu.appendChild(buttonCompact);
                buttonCompact.onclick = function() {
                    try {
                        me.compact();
                        me._onChange();
                    } catch (err) {
                        me._onError(err);
                    }
                }
                ;
                if (this.options.enableSort) {
                    var _sort = document.createElement('button');
                    _sort.type = 'button';
                    _sort.className = 'jsoneditor-sort';
                    _sort.title = Object(i18n["c"])('sortTitleShort');
                    _sort.onclick = function() {
                        me._showSortModal();
                    }
                    ;
                    this.menu.appendChild(_sort);
                }
                if (this.options.enableTransform) {
                    var transform = document.createElement('button');
                    transform.type = 'button';
                    transform.title = Object(i18n["c"])('transformTitleShort');
                    transform.className = 'jsoneditor-transform';
                    transform.onclick = function() {
                        me._showTransformModal();
                    }
                    ;
                    this.menu.appendChild(transform);
                }
                var buttonRepair = document.createElement('button');
                buttonRepair.type = 'button';
                buttonRepair.className = 'jsoneditor-repair';
                buttonRepair.title = Object(i18n["c"])('repairTitle');
                this.menu.appendChild(buttonRepair);
                buttonRepair.onclick = function() {
                    try {
                        me.repair();
                        me._onChange();
                    } catch (err) {
                        me._onError(err);
                    }
                }
                ;
                if (this.mode === 'code') {
                    var undo = document.createElement('button');
                    undo.type = 'button';
                    undo.className = 'jsoneditor-undo jsoneditor-separator';
                    undo.title = Object(i18n["c"])('undo');
                    undo.onclick = function() {
                        _this.aceEditor.getSession().getUndoManager().undo();
                    }
                    ;
                    this.menu.appendChild(undo);
                    this.dom.undo = undo;
                    var redo = document.createElement('button');
                    redo.type = 'button';
                    redo.className = 'jsoneditor-redo';
                    redo.title = Object(i18n["c"])('redo');
                    redo.onclick = function() {
                        _this.aceEditor.getSession().getUndoManager().redo();
                    }
                    ;
                    this.menu.appendChild(redo);
                    this.dom.redo = redo;
                }
                if (this.options && this.options.modes && this.options.modes.length) {
                    this.modeSwitcher = new ModeSwitcher["a"](this.menu,this.options.modes,this.options.mode,function onSwitch(mode) {
                        me.setMode(mode);
                        me.modeSwitcher.focus();
                    }
                    );
                }
                if (this.mode === 'code') {
                    var poweredBy = document.createElement('a');
                    poweredBy.appendChild(document.createTextNode('powered by ace'));
                    poweredBy.href = 'https://ace.c9.io/';
                    poweredBy.target = '_blank';
                    poweredBy.className = 'jsoneditor-poweredBy';
                    poweredBy.onclick = function() {
                        window.open(poweredBy.href, poweredBy.target, 'noopener');
                    }
                    ;
                    this.menu.appendChild(poweredBy);
                }
            }
            var emptyNode = {};
            var isReadOnly = this.options.onEditable && _typeof(this.options.onEditable === 'function') && !this.options.onEditable(emptyNode);
            this.frame.appendChild(this.content);
            this.container.appendChild(this.frame);
            if (this.mode === 'code') {
                this.editorDom = document.createElement('div');
                this.editorDom.style.height = '100%';
                this.editorDom.style.width = '100%';
                this.content.appendChild(this.editorDom);
                var aceEditor = _ace.edit(this.editorDom);
                var aceSession = aceEditor.getSession();
                aceEditor.$blockScrolling = Infinity;
                aceEditor.setTheme(this.theme);
                aceEditor.setOptions({
                    readOnly: isReadOnly
                });
                aceEditor.setShowPrintMargin(false);
                aceEditor.setFontSize('13px');
                aceSession.setMode('ace/mode/json');
                aceSession.setTabSize(this.indentation);
                aceSession.setUseSoftTabs(true);
                aceSession.setUseWrapMode(true);
                var originalSetAnnotations = aceSession.setAnnotations;
                aceSession.setAnnotations = function(annotations) {
                    originalSetAnnotations.call(this, annotations && annotations.length ? annotations : me.annotations);
                }
                ;
                aceEditor.commands.bindKey('Ctrl-L', null);
                aceEditor.commands.bindKey('Command-L', null);
                aceEditor.commands.bindKey('Ctrl-\\', null);
                aceEditor.commands.bindKey('Command-\\', null);
                aceEditor.commands.bindKey('Ctrl-Shift-\\', null);
                aceEditor.commands.bindKey('Command-Shift-\\', null);
                this.aceEditor = aceEditor;
                aceEditor.on('change', this._onChange.bind(this));
                aceEditor.on('changeSelection', this._onSelect.bind(this));
            } else {
                var textarea = document.createElement('textarea');
                textarea.className = 'jsoneditor-text';
                textarea.spellcheck = false;
                this.content.appendChild(textarea);
                this.textarea = textarea;
                this.textarea.readOnly = isReadOnly;
                if (this.textarea.oninput === null) {
                    this.textarea.oninput = this._onChange.bind(this);
                } else {
                    this.textarea.onchange = this._onChange.bind(this);
                }
                textarea.onselect = this._onSelect.bind(this);
                textarea.onmousedown = this._onMouseDown.bind(this);
                textarea.onblur = this._onBlur.bind(this);
            }
            this._updateHistoryButtons();
            this.errorTable = new ErrorTable["a"]({
                errorTableVisible: this.mode === 'text',
                onToggleVisibility: function onToggleVisibility() {
                    me.validate();
                },
                onFocusLine: function onFocusLine(line) {
                    me.isFocused = true;
                    if (!isNaN(line)) {
                        me.setTextSelection({
                            row: line,
                            column: 1
                        }, {
                            row: line,
                            column: 1000
                        });
                    }
                },
                onChangeHeight: function onChangeHeight(height) {
                    var statusBarHeight = me.dom.statusBar ? me.dom.statusBar.clientHeight : 0;
                    var totalHeight = height + statusBarHeight + 1;
                    me.content.style.marginBottom = -totalHeight + 'px';
                    me.content.style.paddingBottom = totalHeight + 'px';
                }
            });
            this.frame.appendChild(this.errorTable.getErrorTable());
            if (options.statusBar) {
                Object(util["addClassName"])(this.content, 'has-status-bar');
                this.curserInfoElements = {};
                var statusBar = document.createElement('div');
                this.dom.statusBar = statusBar;
                statusBar.className = 'jsoneditor-statusbar';
                this.frame.appendChild(statusBar);
                var lnLabel = document.createElement('span');
                lnLabel.className = 'jsoneditor-curserinfo-label';
                lnLabel.innerText = 'Ln:';
                var lnVal = document.createElement('span');
                lnVal.className = 'jsoneditor-curserinfo-val';
                lnVal.innerText = '1';
                statusBar.appendChild(lnLabel);
                statusBar.appendChild(lnVal);
                var colLabel = document.createElement('span');
                colLabel.className = 'jsoneditor-curserinfo-label';
                colLabel.innerText = 'Col:';
                var colVal = document.createElement('span');
                colVal.className = 'jsoneditor-curserinfo-val';
                colVal.innerText = '1';
                statusBar.appendChild(colLabel);
                statusBar.appendChild(colVal);
                this.curserInfoElements.colVal = colVal;
                this.curserInfoElements.lnVal = lnVal;
                var countLabel = document.createElement('span');
                countLabel.className = 'jsoneditor-curserinfo-label';
                countLabel.innerText = 'characters selected';
                countLabel.style.display = 'none';
                var countVal = document.createElement('span');
                countVal.className = 'jsoneditor-curserinfo-count';
                countVal.innerText = '0';
                countVal.style.display = 'none';
                this.curserInfoElements.countLabel = countLabel;
                this.curserInfoElements.countVal = countVal;
                statusBar.appendChild(countVal);
                statusBar.appendChild(countLabel);
                statusBar.appendChild(this.errorTable.getErrorCounter());
                statusBar.appendChild(this.errorTable.getWarningIcon());
                statusBar.appendChild(this.errorTable.getErrorIcon());
            }
            this.setSchema(this.options.schema, this.options.schemaRefs);
        }
        ;
        textmode._onChange = function() {
            var _this2 = this;
            if (this.onChangeDisabled) {
                return;
            }
            setTimeout(function() {
                return _this2._updateHistoryButtons();
            });
            this._debouncedValidate();
            if (this.options.onChange) {
                try {
                    this.options.onChange();
                } catch (err) {
                    console.error('Error in onChange callback: ', err);
                }
            }
            if (this.options.onChangeText) {
                try {
                    this.options.onChangeText(this.getText());
                } catch (err) {
                    console.error('Error in onChangeText callback: ', err);
                }
            }
        }
        ;
        textmode._updateHistoryButtons = function() {
            if (this.aceEditor && this.dom.undo && this.dom.redo) {
                var undoManager = this.aceEditor.getSession().getUndoManager();
                if (undoManager && undoManager.hasUndo && undoManager.hasRedo) {
                    this.dom.undo.disabled = !undoManager.hasUndo();
                    this.dom.redo.disabled = !undoManager.hasRedo();
                }
            }
        }
        ;
        textmode._showSortModal = function() {
            var me = this;
            var container = this.options.modalAnchor || constants["a"];
            var json = this.get();
            function onSort(sortedBy) {
                if (Array.isArray(json)) {
                    var sortedJson = Object(util["sort"])(json, sortedBy.path, sortedBy.direction);
                    me.sortedBy = sortedBy;
                    me.update(sortedJson);
                }
                if (Object(util["isObject"])(json)) {
                    var _sortedJson = Object(util["sortObjectKeys"])(json, sortedBy.direction);
                    me.sortedBy = sortedBy;
                    me.update(_sortedJson);
                }
            }
            Object(showSortModal["showSortModal"])(container, json, onSort, me.sortedBy);
        }
        ;
        textmode._showTransformModal = function() {
            var _this3 = this;
            var _this$options = this.options
              , modalAnchor = _this$options.modalAnchor
              , createQuery = _this$options.createQuery
              , executeQuery = _this$options.executeQuery
              , queryDescription = _this$options.queryDescription;
            var json = this.get();
            Object(showTransformModal["showTransformModal"])({
                container: modalAnchor || constants["a"],
                json: json,
                queryDescription: queryDescription,
                createQuery: createQuery,
                executeQuery: executeQuery,
                onTransform: function onTransform(query) {
                    var updatedJson = executeQuery(json, query);
                    _this3.update(updatedJson);
                }
            });
        }
        ;
        textmode._onSelect = function() {
            this._updateCursorInfo();
            this._emitSelectionChange();
        }
        ;
        textmode._onKeyDown = function(event) {
            var keynum = event.which || event.keyCode;
            var handled = false;
            if (keynum === 220 && event.ctrlKey) {
                if (event.shiftKey) {
                    this.compact();
                    this._onChange();
                } else {
                    this.format();
                    this._onChange();
                }
                handled = true;
            }
            if (handled) {
                event.preventDefault();
                event.stopPropagation();
            }
            this._updateCursorInfo();
            this._emitSelectionChange();
        }
        ;
        textmode._onMouseDown = function() {
            this._updateCursorInfo();
            this._emitSelectionChange();
        }
        ;
        textmode._onBlur = function() {
            var me = this;
            setTimeout(function() {
                if (!me.isFocused) {
                    me._updateCursorInfo();
                    me._emitSelectionChange();
                }
                me.isFocused = false;
            });
        }
        ;
        textmode._updateCursorInfo = function() {
            var me = this;
            var line, col, count;
            if (this.textarea) {
                setTimeout(function() {
                    var selectionRange = Object(util["getInputSelection"])(me.textarea);
                    if (selectionRange.startIndex !== selectionRange.endIndex) {
                        count = selectionRange.endIndex - selectionRange.startIndex;
                    }
                    if (count && me.cursorInfo && me.cursorInfo.line === selectionRange.end.row && me.cursorInfo.column === selectionRange.end.column) {
                        line = selectionRange.start.row;
                        col = selectionRange.start.column;
                    } else {
                        line = selectionRange.end.row;
                        col = selectionRange.end.column;
                    }
                    me.cursorInfo = {
                        line: line,
                        column: col,
                        count: count
                    };
                    if (me.options.statusBar) {
                        updateDisplay();
                    }
                }, 0);
            } else if (this.aceEditor && this.curserInfoElements) {
                var curserPos = this.aceEditor.getCursorPosition();
                var selectedText = this.aceEditor.getSelectedText();
                line = curserPos.row + 1;
                col = curserPos.column + 1;
                count = selectedText.length;
                me.cursorInfo = {
                    line: line,
                    column: col,
                    count: count
                };
                if (this.options.statusBar) {
                    updateDisplay();
                }
            }
            function updateDisplay() {
                if (me.curserInfoElements.countVal.innerText !== count) {
                    me.curserInfoElements.countVal.innerText = count;
                    me.curserInfoElements.countVal.style.display = count ? 'inline' : 'none';
                    me.curserInfoElements.countLabel.style.display = count ? 'inline' : 'none';
                }
                me.curserInfoElements.lnVal.innerText = line;
                me.curserInfoElements.colVal.innerText = col;
            }
        }
        ;
        textmode._emitSelectionChange = function() {
            if (this._selectionChangedHandler) {
                var currentSelection = this.getTextSelection();
                this._selectionChangedHandler(currentSelection.start, currentSelection.end, currentSelection.text);
            }
        }
        ;
        textmode._refreshAnnotations = function() {
            var session = this.aceEditor && this.aceEditor.getSession();
            if (session) {
                var errEnnotations = session.getAnnotations().filter(function(annotation) {
                    return annotation.type === 'error';
                });
                session.setAnnotations(errEnnotations);
            }
        }
        ;
        textmode.destroy = function() {
            if (this.aceEditor) {
                this.aceEditor.destroy();
                this.aceEditor = null;
            }
            if (this.frame && this.container && this.frame.parentNode === this.container) {
                this.container.removeChild(this.frame);
            }
            if (this.modeSwitcher) {
                this.modeSwitcher.destroy();
                this.modeSwitcher = null;
            }
            this.textarea = null;
            this._debouncedValidate = null;
            this.frameFocusTracker.destroy();
        }
        ;
        textmode.compact = function() {
            var json = this.get();
            var text = JSON.stringify(json);
            this.updateText(text);
        }
        ;
        textmode.format = function() {
            var json = this.get();
            var text = JSON.stringify(json, null, this.indentation);
            this.updateText(text);
        }
        ;
        textmode.repair = function() {
            var text = this.getText();
            try {
                var repairedText = simpleJsonRepair_min_default()(text);
                this.updateText(repairedText);
            } catch (err) {}
        }
        ;
        textmode.focus = function() {
            if (this.textarea) {
                this.textarea.focus();
            }
            if (this.aceEditor) {
                this.aceEditor.focus();
            }
        }
        ;
        textmode.resize = function() {
            if (this.aceEditor) {
                var force = false;
                this.aceEditor.resize(force);
            }
        }
        ;
        textmode.set = function(json) {
            this.setText(JSON.stringify(json, null, this.indentation));
        }
        ;
        textmode.update = function(json) {
            this.updateText(JSON.stringify(json, null, this.indentation));
        }
        ;
        textmode.get = function() {
            var text = this.getText();
            return Object(util["parse"])(text);
        }
        ;
        textmode.getText = function() {
            if (this.textarea) {
                return this.textarea.value;
            }
            if (this.aceEditor) {
                return this.aceEditor.getValue();
            }
            return '';
        }
        ;
        textmode._setText = function(jsonText, clearHistory) {
            var _this4 = this;
            var text = this.options.escapeUnicode === true ? Object(util["escapeUnicodeChars"])(jsonText) : jsonText;
            if (this.textarea) {
                this.textarea.value = text;
            }
            if (this.aceEditor) {
                this.onChangeDisabled = true;
                this.aceEditor.setValue(text, -1);
                this.onChangeDisabled = false;
                if (clearHistory) {
                    var me = this;
                    setTimeout(function() {
                        if (me.aceEditor) {
                            me.aceEditor.session.getUndoManager().reset();
                        }
                    });
                }
                setTimeout(function() {
                    return _this4._updateHistoryButtons();
                });
            }
            this._debouncedValidate();
        }
        ;
        textmode.setText = function(jsonText) {
            this._setText(jsonText, true);
        }
        ;
        textmode.updateText = function(jsonText) {
            if (this.getText() === jsonText) {
                return;
            }
            this._setText(jsonText, false);
        }
        ;
        textmode.validate = function() {
            var _this5 = this;
            var schemaErrors = [];
            var parseErrors = [];
            var json;
            try {
                json = this.get();
                if (this.validateSchema) {
                    var valid = this.validateSchema(json);
                    if (!valid) {
                        schemaErrors = this.validateSchema.errors.map(function(error) {
                            error.type = 'validation';
                            return Object(util["improveSchemaError"])(error);
                        });
                    }
                }
                this.validationSequence = (this.validationSequence || 0) + 1;
                var me = this;
                var seq = this.validationSequence;
                validateCustom(json, this.options.onValidate).then(function(customValidationErrors) {
                    if (seq === me.validationSequence) {
                        var errors = schemaErrors.concat(parseErrors).concat(customValidationErrors);
                        me._renderErrors(errors);
                        if (typeof _this5.options.onValidationError === 'function') {
                            if (Object(util["isValidationErrorChanged"])(errors, _this5.lastSchemaErrors)) {
                                _this5.options.onValidationError.call(_this5, errors);
                            }
                            _this5.lastSchemaErrors = errors;
                        }
                    }
                })["catch"](function(err) {
                    console.error('Custom validation function did throw an error', err);
                });
            } catch (err) {
                if (this.getText()) {
                    var match = /\w*line\s*(\d+)\w*/g.exec(err.message);
                    var line;
                    if (match) {
                        line = +match[1];
                    }
                    parseErrors = [{
                        type: 'error',
                        message: err.message.replace(/\n/g, '<br>'),
                        line: line
                    }];
                }
                this._renderErrors(parseErrors);
                if (typeof this.options.onValidationError === 'function') {
                    if (Object(util["isValidationErrorChanged"])(parseErrors, this.lastSchemaErrors)) {
                        this.options.onValidationError.call(this, parseErrors);
                    }
                    this.lastSchemaErrors = parseErrors;
                }
            }
        }
        ;
        textmode._renderErrors = function(errors) {
            var jsonText = this.getText();
            var errorPaths = [];
            errors.reduce(function(acc, curr) {
                if (typeof curr.dataPath === 'string' && acc.indexOf(curr.dataPath) === -1) {
                    acc.push(curr.dataPath);
                }
                return acc;
            }, errorPaths);
            var errorLocations = Object(util["getPositionForPath"])(jsonText, errorPaths);
            if (this.aceEditor) {
                this.annotations = errorLocations.map(function(errLoc) {
                    var validationErrors = errors.filter(function(err) {
                        return err.dataPath === errLoc.path;
                    });
                    var message = validationErrors.map(function(err) {
                        return err.message;
                    }).join('\n');
                    if (message) {
                        return {
                            row: errLoc.line,
                            column: errLoc.column,
                            text: 'Schema validation error' + (validationErrors.length !== 1 ? 's' : '') + ': \n' + message,
                            type: 'warning',
                            source: 'jsoneditor'
                        };
                    }
                    return {};
                });
                this._refreshAnnotations();
            }
            this.errorTable.setErrors(errors, errorLocations);
            if (this.aceEditor) {
                var force = false;
                this.aceEditor.resize(force);
            }
        }
        ;
        textmode.getTextSelection = function() {
            var selection = {};
            if (this.textarea) {
                var selectionRange = Object(util["getInputSelection"])(this.textarea);
                if (this.cursorInfo && this.cursorInfo.line === selectionRange.end.row && this.cursorInfo.column === selectionRange.end.column) {
                    selection.start = selectionRange.end;
                    selection.end = selectionRange.start;
                } else {
                    selection = selectionRange;
                }
                return {
                    start: selection.start,
                    end: selection.end,
                    text: this.textarea.value.substring(selectionRange.startIndex, selectionRange.endIndex)
                };
            }
            if (this.aceEditor) {
                var aceSelection = this.aceEditor.getSelection();
                var selectedText = this.aceEditor.getSelectedText();
                var range = aceSelection.getRange();
                var lead = aceSelection.getSelectionLead();
                if (lead.row === range.end.row && lead.column === range.end.column) {
                    selection = range;
                } else {
                    selection.start = range.end;
                    selection.end = range.start;
                }
                return {
                    start: {
                        row: selection.start.row + 1,
                        column: selection.start.column + 1
                    },
                    end: {
                        row: selection.end.row + 1,
                        column: selection.end.column + 1
                    },
                    text: selectedText
                };
            }
        }
        ;
        textmode.onTextSelectionChange = function(callback) {
            if (typeof callback === 'function') {
                this._selectionChangedHandler = Object(util["debounce"])(callback, this.DEBOUNCE_INTERVAL);
            }
        }
        ;
        textmode.setTextSelection = function(startPos, endPos) {
            if (!startPos || !endPos)
                return;
            if (this.textarea) {
                var startIndex = Object(util["getIndexForPosition"])(this.textarea, startPos.row, startPos.column);
                var endIndex = Object(util["getIndexForPosition"])(this.textarea, endPos.row, endPos.column);
                if (startIndex > -1 && endIndex > -1) {
                    if (this.textarea.setSelectionRange) {
                        this.textarea.focus();
                        this.textarea.setSelectionRange(startIndex, endIndex);
                    } else if (this.textarea.createTextRange) {
                        var range = this.textarea.createTextRange();
                        range.collapse(true);
                        range.moveEnd('character', endIndex);
                        range.moveStart('character', startIndex);
                        range.select();
                    }
                    var rows = (this.textarea.value.match(/\n/g) || []).length + 1;
                    var lineHeight = this.textarea.scrollHeight / rows;
                    var selectionScrollPos = startPos.row * lineHeight;
                    this.textarea.scrollTop = selectionScrollPos > this.textarea.clientHeight ? selectionScrollPos - this.textarea.clientHeight / 2 : 0;
                }
            } else if (this.aceEditor) {
                var _range = {
                    start: {
                        row: startPos.row - 1,
                        column: startPos.column - 1
                    },
                    end: {
                        row: endPos.row - 1,
                        column: endPos.column - 1
                    }
                };
                this.aceEditor.selection.setRange(_range);
                this.aceEditor.scrollToLine(startPos.row - 1, true);
            }
        }
        ;
        function load() {
            try {
                this.format();
            } catch (err) {}
        }
        var textModeMixins = [{
            mode: 'text',
            mixin: textmode,
            data: 'text',
            load: load
        }, {
            mode: 'code',
            mixin: textmode,
            data: 'text',
            load: load
        }];
    }
    ), (function(module, exports, __webpack_require__) {
        var jsonlint = function() {
            var parser = {
                trace: function trace() {},
                yy: {},
                symbols_: {
                    "error": 2,
                    "JSONString": 3,
                    "STRING": 4,
                    "JSONNumber": 5,
                    "NUMBER": 6,
                    "JSONNullLiteral": 7,
                    "NULL": 8,
                    "JSONBooleanLiteral": 9,
                    "TRUE": 10,
                    "FALSE": 11,
                    "JSONText": 12,
                    "JSONValue": 13,
                    "EOF": 14,
                    "JSONObject": 15,
                    "JSONArray": 16,
                    "{": 17,
                    "}": 18,
                    "JSONMemberList": 19,
                    "JSONMember": 20,
                    ":": 21,
                    ",": 22,
                    "[": 23,
                    "]": 24,
                    "JSONElementList": 25,
                    "$accept": 0,
                    "$end": 1
                },
                terminals_: {
                    2: "error",
                    4: "STRING",
                    6: "NUMBER",
                    8: "NULL",
                    10: "TRUE",
                    11: "FALSE",
                    14: "EOF",
                    17: "{",
                    18: "}",
                    21: ":",
                    22: ",",
                    23: "[",
                    24: "]"
                },
                productions_: [0, [3, 1], [5, 1], [7, 1], [9, 1], [9, 1], [12, 2], [13, 1], [13, 1], [13, 1], [13, 1], [13, 1], [13, 1], [15, 2], [15, 3], [20, 3], [19, 1], [19, 3], [16, 2], [16, 3], [25, 1], [25, 3]],
                performAction: function anonymous(yytext, yyleng, yylineno, yy, yystate, $$, _$) {
                    var $0 = $$.length - 1;
                    switch (yystate) {
                    case 1:
                        this.$ = yytext.replace(/\\(\\|")/g, "$" + "1").replace(/\\n/g, '\n').replace(/\\r/g, '\r').replace(/\\t/g, '\t').replace(/\\v/g, '\v').replace(/\\f/g, '\f').replace(/\\b/g, '\b');
                        break;
                    case 2:
                        this.$ = Number(yytext);
                        break;
                    case 3:
                        this.$ = null;
                        break;
                    case 4:
                        this.$ = true;
                        break;
                    case 5:
                        this.$ = false;
                        break;
                    case 6:
                        return this.$ = $$[$0 - 1];
                        break;
                    case 13:
                        this.$ = {};
                        break;
                    case 14:
                        this.$ = $$[$0 - 1];
                        break;
                    case 15:
                        this.$ = [$$[$0 - 2], $$[$0]];
                        break;
                    case 16:
                        this.$ = {};
                        this.$[$$[$0][0]] = $$[$0][1];
                        break;
                    case 17:
                        this.$ = $$[$0 - 2];
                        $$[$0 - 2][$$[$0][0]] = $$[$0][1];
                        break;
                    case 18:
                        this.$ = [];
                        break;
                    case 19:
                        this.$ = $$[$0 - 1];
                        break;
                    case 20:
                        this.$ = [$$[$0]];
                        break;
                    case 21:
                        this.$ = $$[$0 - 2];
                        $$[$0 - 2].push($$[$0]);
                        break;
                    }
                },
                table: [{
                    3: 5,
                    4: [1, 12],
                    5: 6,
                    6: [1, 13],
                    7: 3,
                    8: [1, 9],
                    9: 4,
                    10: [1, 10],
                    11: [1, 11],
                    12: 1,
                    13: 2,
                    15: 7,
                    16: 8,
                    17: [1, 14],
                    23: [1, 15]
                }, {
                    1: [3]
                }, {
                    14: [1, 16]
                }, {
                    14: [2, 7],
                    18: [2, 7],
                    22: [2, 7],
                    24: [2, 7]
                }, {
                    14: [2, 8],
                    18: [2, 8],
                    22: [2, 8],
                    24: [2, 8]
                }, {
                    14: [2, 9],
                    18: [2, 9],
                    22: [2, 9],
                    24: [2, 9]
                }, {
                    14: [2, 10],
                    18: [2, 10],
                    22: [2, 10],
                    24: [2, 10]
                }, {
                    14: [2, 11],
                    18: [2, 11],
                    22: [2, 11],
                    24: [2, 11]
                }, {
                    14: [2, 12],
                    18: [2, 12],
                    22: [2, 12],
                    24: [2, 12]
                }, {
                    14: [2, 3],
                    18: [2, 3],
                    22: [2, 3],
                    24: [2, 3]
                }, {
                    14: [2, 4],
                    18: [2, 4],
                    22: [2, 4],
                    24: [2, 4]
                }, {
                    14: [2, 5],
                    18: [2, 5],
                    22: [2, 5],
                    24: [2, 5]
                }, {
                    14: [2, 1],
                    18: [2, 1],
                    21: [2, 1],
                    22: [2, 1],
                    24: [2, 1]
                }, {
                    14: [2, 2],
                    18: [2, 2],
                    22: [2, 2],
                    24: [2, 2]
                }, {
                    3: 20,
                    4: [1, 12],
                    18: [1, 17],
                    19: 18,
                    20: 19
                }, {
                    3: 5,
                    4: [1, 12],
                    5: 6,
                    6: [1, 13],
                    7: 3,
                    8: [1, 9],
                    9: 4,
                    10: [1, 10],
                    11: [1, 11],
                    13: 23,
                    15: 7,
                    16: 8,
                    17: [1, 14],
                    23: [1, 15],
                    24: [1, 21],
                    25: 22
                }, {
                    1: [2, 6]
                }, {
                    14: [2, 13],
                    18: [2, 13],
                    22: [2, 13],
                    24: [2, 13]
                }, {
                    18: [1, 24],
                    22: [1, 25]
                }, {
                    18: [2, 16],
                    22: [2, 16]
                }, {
                    21: [1, 26]
                }, {
                    14: [2, 18],
                    18: [2, 18],
                    22: [2, 18],
                    24: [2, 18]
                }, {
                    22: [1, 28],
                    24: [1, 27]
                }, {
                    22: [2, 20],
                    24: [2, 20]
                }, {
                    14: [2, 14],
                    18: [2, 14],
                    22: [2, 14],
                    24: [2, 14]
                }, {
                    3: 20,
                    4: [1, 12],
                    20: 29
                }, {
                    3: 5,
                    4: [1, 12],
                    5: 6,
                    6: [1, 13],
                    7: 3,
                    8: [1, 9],
                    9: 4,
                    10: [1, 10],
                    11: [1, 11],
                    13: 30,
                    15: 7,
                    16: 8,
                    17: [1, 14],
                    23: [1, 15]
                }, {
                    14: [2, 19],
                    18: [2, 19],
                    22: [2, 19],
                    24: [2, 19]
                }, {
                    3: 5,
                    4: [1, 12],
                    5: 6,
                    6: [1, 13],
                    7: 3,
                    8: [1, 9],
                    9: 4,
                    10: [1, 10],
                    11: [1, 11],
                    13: 31,
                    15: 7,
                    16: 8,
                    17: [1, 14],
                    23: [1, 15]
                }, {
                    18: [2, 17],
                    22: [2, 17]
                }, {
                    18: [2, 15],
                    22: [2, 15]
                }, {
                    22: [2, 21],
                    24: [2, 21]
                }],
                defaultActions: {
                    16: [2, 6]
                },
                parseError: function parseError(str, hash) {
                    throw new Error(str);
                },
                parse: function parse(input) {
                    var self = this
                      , stack = [0]
                      , vstack = [null]
                      , lstack = []
                      , table = this.table
                      , yytext = ''
                      , yylineno = 0
                      , yyleng = 0
                      , recovering = 0
                      , TERROR = 2
                      , EOF = 1;
                    this.lexer.setInput(input);
                    this.lexer.yy = this.yy;
                    this.yy.lexer = this.lexer;
                    if (typeof this.lexer.yylloc == 'undefined')
                        this.lexer.yylloc = {};
                    var yyloc = this.lexer.yylloc;
                    lstack.push(yyloc);
                    if (typeof this.yy.parseError === 'function')
                        this.parseError = this.yy.parseError;
                    function popStack(n) {
                        stack.length = stack.length - 2 * n;
                        vstack.length = vstack.length - n;
                        lstack.length = lstack.length - n;
                    }
                    function lex() {
                        var token;
                        token = self.lexer.lex() || 1;
                        if (typeof token !== 'number') {
                            token = self.symbols_[token] || token;
                        }
                        return token;
                    }
                    var symbol, preErrorSymbol, state, action, a, r, yyval = {}, p, len, newState, expected;
                    while (true) {
                        state = stack[stack.length - 1];
                        if (this.defaultActions[state]) {
                            action = this.defaultActions[state];
                        } else {
                            if (symbol == null)
                                symbol = lex();
                            action = table[state] && table[state][symbol];
                        }
                        _handle_error: if (typeof action === 'undefined' || !action.length || !action[0]) {
                            if (!recovering) {
                                expected = [];
                                for (p in table[state]) {
                                    if (this.terminals_[p] && p > 2) {
                                        expected.push("'" + this.terminals_[p] + "'");
                                    }
                                }
                                var errStr = '';
                                if (this.lexer.showPosition) {
                                    errStr = 'Parse error on line ' + (yylineno + 1) + ":\n" + this.lexer.showPosition() + "\nExpecting " + expected.join(', ') + ", got '" + this.terminals_[symbol] + "'";
                                } else {
                                    errStr = 'Parse error on line ' + (yylineno + 1) + ": Unexpected " + (symbol == 1 ? "end of input" : "'" + (this.terminals_[symbol] || symbol) + "'");
                                }
                                this.parseError(errStr, {
                                    text: this.lexer.match,
                                    token: this.terminals_[symbol] || symbol,
                                    line: this.lexer.yylineno,
                                    loc: yyloc,
                                    expected: expected
                                });
                            }
                            if (recovering == 3) {
                                if (symbol == EOF) {
                                    throw new Error(errStr || 'Parsing halted.');
                                }
                                yyleng = this.lexer.yyleng;
                                yytext = this.lexer.yytext;
                                yylineno = this.lexer.yylineno;
                                yyloc = this.lexer.yylloc;
                                symbol = lex();
                            }
                            while (1) {
                                if (TERROR.toString()in table[state]) {
                                    break;
                                }
                                if (state == 0) {
                                    throw new Error(errStr || 'Parsing halted.');
                                }
                                popStack(1);
                                state = stack[stack.length - 1];
                            }
                            preErrorSymbol = symbol;
                            symbol = TERROR;
                            state = stack[stack.length - 1];
                            action = table[state] && table[state][TERROR];
                            recovering = 3;
                        }
                        if (action[0]instanceof Array && action.length > 1) {
                            throw new Error('Parse Error: multiple actions possible at state: ' + state + ', token: ' + symbol);
                        }
                        switch (action[0]) {
                        case 1:
                            stack.push(symbol);
                            vstack.push(this.lexer.yytext);
                            lstack.push(this.lexer.yylloc);
                            stack.push(action[1]);
                            symbol = null;
                            if (!preErrorSymbol) {
                                yyleng = this.lexer.yyleng;
                                yytext = this.lexer.yytext;
                                yylineno = this.lexer.yylineno;
                                yyloc = this.lexer.yylloc;
                                if (recovering > 0)
                                    recovering--;
                            } else {
                                symbol = preErrorSymbol;
                                preErrorSymbol = null;
                            }
                            break;
                        case 2:
                            len = this.productions_[action[1]][1];
                            yyval.$ = vstack[vstack.length - len];
                            yyval._$ = {
                                first_line: lstack[lstack.length - (len || 1)].first_line,
                                last_line: lstack[lstack.length - 1].last_line,
                                first_column: lstack[lstack.length - (len || 1)].first_column,
                                last_column: lstack[lstack.length - 1].last_column
                            };
                            r = this.performAction.call(yyval, yytext, yyleng, yylineno, this.yy, action[1], vstack, lstack);
                            if (typeof r !== 'undefined') {
                                return r;
                            }
                            if (len) {
                                stack = stack.slice(0, -1 * len * 2);
                                vstack = vstack.slice(0, -1 * len);
                                lstack = lstack.slice(0, -1 * len);
                            }
                            stack.push(this.productions_[action[1]][0]);
                            vstack.push(yyval.$);
                            lstack.push(yyval._$);
                            newState = table[stack[stack.length - 2]][stack[stack.length - 1]];
                            stack.push(newState);
                            break;
                        case 3:
                            return true;
                        }
                    }
                    return true;
                }
            };
            var lexer = function() {
                var lexer = {
                    EOF: 1,
                    parseError: function parseError(str, hash) {
                        if (this.yy.parseError) {
                            this.yy.parseError(str, hash);
                        } else {
                            throw new Error(str);
                        }
                    },
                    setInput: function setInput(input) {
                        this._input = input;
                        this._more = this._less = this.done = false;
                        this.yylineno = this.yyleng = 0;
                        this.yytext = this.matched = this.match = '';
                        this.conditionStack = ['INITIAL'];
                        this.yylloc = {
                            first_line: 1,
                            first_column: 0,
                            last_line: 1,
                            last_column: 0
                        };
                        return this;
                    },
                    input: function input() {
                        var ch = this._input[0];
                        this.yytext += ch;
                        this.yyleng++;
                        this.match += ch;
                        this.matched += ch;
                        var lines = ch.match(/\n/);
                        if (lines)
                            this.yylineno++;
                        this._input = this._input.slice(1);
                        return ch;
                    },
                    unput: function unput(ch) {
                        this._input = ch + this._input;
                        return this;
                    },
                    more: function more() {
                        this._more = true;
                        return this;
                    },
                    less: function less(n) {
                        this._input = this.match.slice(n) + this._input;
                    },
                    pastInput: function pastInput() {
                        var past = this.matched.substr(0, this.matched.length - this.match.length);
                        return (past.length > 20 ? '...' : '') + past.substr(-20).replace(/\n/g, "");
                    },
                    upcomingInput: function upcomingInput() {
                        var next = this.match;
                        if (next.length < 20) {
                            next += this._input.substr(0, 20 - next.length);
                        }
                        return (next.substr(0, 20) + (next.length > 20 ? '...' : '')).replace(/\n/g, "");
                    },
                    showPosition: function showPosition() {
                        var pre = this.pastInput();
                        var c = new Array(pre.length + 1).join("-");
                        return pre + this.upcomingInput() + "\n" + c + "^";
                    },
                    next: function next() {
                        if (this.done) {
                            return this.EOF;
                        }
                        if (!this._input)
                            this.done = true;
                        var token, match, tempMatch, index, col, lines;
                        if (!this._more) {
                            this.yytext = '';
                            this.match = '';
                        }
                        var rules = this._currentRules();
                        for (var i = 0; i < rules.length; i++) {
                            tempMatch = this._input.match(this.rules[rules[i]]);
                            if (tempMatch && (!match || tempMatch[0].length > match[0].length)) {
                                match = tempMatch;
                                index = i;
                                if (!this.options.flex)
                                    break;
                            }
                        }
                        if (match) {
                            lines = match[0].match(/\n.*/g);
                            if (lines)
                                this.yylineno += lines.length;
                            this.yylloc = {
                                first_line: this.yylloc.last_line,
                                last_line: this.yylineno + 1,
                                first_column: this.yylloc.last_column,
                                last_column: lines ? lines[lines.length - 1].length - 1 : this.yylloc.last_column + match[0].length
                            };
                            this.yytext += match[0];
                            this.match += match[0];
                            this.yyleng = this.yytext.length;
                            this._more = false;
                            this._input = this._input.slice(match[0].length);
                            this.matched += match[0];
                            token = this.performAction.call(this, this.yy, this, rules[index], this.conditionStack[this.conditionStack.length - 1]);
                            if (this.done && this._input)
                                this.done = false;
                            if (token)
                                return token;
                            else
                                return;
                        }
                        if (this._input === "") {
                            return this.EOF;
                        } else {
                            this.parseError('Lexical error on line ' + (this.yylineno + 1) + '. Unrecognized text.\n' + this.showPosition(), {
                                text: "",
                                token: null,
                                line: this.yylineno
                            });
                        }
                    },
                    lex: function lex() {
                        var r = this.next();
                        if (typeof r !== 'undefined') {
                            return r;
                        } else {
                            return this.lex();
                        }
                    },
                    begin: function begin(condition) {
                        this.conditionStack.push(condition);
                    },
                    popState: function popState() {
                        return this.conditionStack.pop();
                    },
                    _currentRules: function _currentRules() {
                        return this.conditions[this.conditionStack[this.conditionStack.length - 1]].rules;
                    },
                    topState: function topState() {
                        return this.conditionStack[this.conditionStack.length - 2];
                    },
                    pushState: function begin(condition) {
                        this.begin(condition);
                    }
                };
                lexer.options = {};
                lexer.performAction = function anonymous(yy, yy_, $avoiding_name_collisions, YY_START) {
                    var YYSTATE = YY_START;
                    switch ($avoiding_name_collisions) {
                    case 0:
                        break;
                    case 1:
                        return 6;
                        break;
                    case 2:
                        yy_.yytext = yy_.yytext.substr(1, yy_.yyleng - 2);
                        return 4;
                        break;
                    case 3:
                        return 17;
                        break;
                    case 4:
                        return 18;
                        break;
                    case 5:
                        return 23;
                        break;
                    case 6:
                        return 24;
                        break;
                    case 7:
                        return 22;
                        break;
                    case 8:
                        return 21;
                        break;
                    case 9:
                        return 10;
                        break;
                    case 10:
                        return 11;
                        break;
                    case 11:
                        return 8;
                        break;
                    case 12:
                        return 14;
                        break;
                    case 13:
                        return 'INVALID';
                        break;
                    }
                }
                ;
                lexer.rules = [/^(?:\s+)/, /^(?:(-?([0-9]|[1-9][0-9]+))(\.[0-9]+)?([eE][-+]?[0-9]+)?\b)/, /^(?:"(?:\\[\\"bfnrt/]|\\u[a-fA-F0-9]{4}|[^\\\0-\x09\x0a-\x1f"])*")/, /^(?:\{)/, /^(?:\})/, /^(?:\[)/, /^(?:\])/, /^(?:,)/, /^(?::)/, /^(?:true\b)/, /^(?:false\b)/, /^(?:null\b)/, /^(?:$)/, /^(?:.)/];
                lexer.conditions = {
                    "INITIAL": {
                        "rules": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
                        "inclusive": true
                    }
                };
                ;return lexer;
            }();
            parser.lexer = lexer;
            return parser;
        }();
        if (true) {
            exports.parser = jsonlint;
            exports.parse = jsonlint.parse.bind(jsonlint);
        }
    }
    ), (function(module, exports) {
        if (typeof Element !== 'undefined') {
            (function() {
                function polyfill(item) {
                    if ('remove'in item) {
                        return;
                    }
                    Object.defineProperty(item, 'remove', {
                        configurable: true,
                        enumerable: true,
                        writable: true,
                        value: function remove() {
                            if (this.parentNode !== undefined) {
                                this.parentNode.removeChild(this);
                            }
                        }
                    });
                }
                if (typeof window.Element !== 'undefined') {
                    polyfill(window.Element.prototype);
                }
                if (typeof window.CharacterData !== 'undefined') {
                    polyfill(window.CharacterData.prototype);
                }
                if (typeof window.DocumentType !== 'undefined') {
                    polyfill(window.DocumentType.prototype);
                }
            }
            )();
        }
        if (!Array.prototype.findIndex) {
            Object.defineProperty(Array.prototype, 'findIndex', {
                value: function value(predicate) {
                    for (var i = 0; i < this.length; i++) {
                        var element = this[i];
                        if (predicate.call(this, element, i, this)) {
                            return i;
                        }
                    }
                    return -1;
                },
                configurable: true,
                writable: true
            });
        }
        if (!Array.prototype.find) {
            Object.defineProperty(Array.prototype, 'find', {
                value: function value(predicate) {
                    var i = this.findIndex(predicate);
                    return this[i];
                },
                configurable: true,
                writable: true
            });
        }
        if (!String.prototype.trim) {
            String.prototype.trim = function() {
                return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
            }
            ;
        }
    }
    ), (function(module, exports, __webpack_require__) {
        "use strict";
        var escapedChars = {
            'b': '\b',
            'f': '\f',
            'n': '\n',
            'r': '\r',
            't': '\t',
            '"': '"',
            '/': '/',
            '\\': '\\'
        };
        var A_CODE = 'a'.charCodeAt();
        exports.parse = function(source, _, options) {
            var pointers = {};
            var line = 0;
            var column = 0;
            var pos = 0;
            var bigint = options && options.bigint && typeof BigInt != 'undefined';
            return {
                data: _parse('', true),
                pointers: pointers
            };
            function _parse(ptr, topLevel) {
                whitespace();
                var data;
                map(ptr, 'value');
                var char = getChar();
                switch (char) {
                case 't':
                    read('rue');
                    data = true;
                    break;
                case 'f':
                    read('alse');
                    data = false;
                    break;
                case 'n':
                    read('ull');
                    data = null;
                    break;
                case '"':
                    data = parseString();
                    break;
                case '[':
                    data = parseArray(ptr);
                    break;
                case '{':
                    data = parseObject(ptr);
                    break;
                default:
                    backChar();
                    if ('-0123456789'.indexOf(char) >= 0)
                        data = parseNumber();
                    else
                        unexpectedToken();
                }
                map(ptr, 'valueEnd');
                whitespace();
                if (topLevel && pos < source.length)
                    unexpectedToken();
                return data;
            }
            function whitespace() {
                loop: while (pos < source.length) {
                    switch (source[pos]) {
                    case ' ':
                        column++;
                        break;
                    case '\t':
                        column += 4;
                        break;
                    case '\r':
                        column = 0;
                        break;
                    case '\n':
                        column = 0;
                        line++;
                        break;
                    default:
                        break loop;
                    }
                    pos++;
                }
            }
            function parseString() {
                var str = '';
                var char;
                while (true) {
                    char = getChar();
                    if (char == '"') {
                        break;
                    } else if (char == '\\') {
                        char = getChar();
                        if (char in escapedChars)
                            str += escapedChars[char];
                        else if (char == 'u')
                            str += getCharCode();
                        else
                            wasUnexpectedToken();
                    } else {
                        str += char;
                    }
                }
                return str;
            }
            function parseNumber() {
                var numStr = '';
                var integer = true;
                if (source[pos] == '-')
                    numStr += getChar();
                numStr += source[pos] == '0' ? getChar() : getDigits();
                if (source[pos] == '.') {
                    numStr += getChar() + getDigits();
                    integer = false;
                }
                if (source[pos] == 'e' || source[pos] == 'E') {
                    numStr += getChar();
                    if (source[pos] == '+' || source[pos] == '-')
                        numStr += getChar();
                    numStr += getDigits();
                    integer = false;
                }
                var result = +numStr;
                return bigint && integer && (result > Number.MAX_SAFE_INTEGER || result < Number.MIN_SAFE_INTEGER) ? BigInt(numStr) : result;
            }
            function parseArray(ptr) {
                whitespace();
                var arr = [];
                var i = 0;
                if (getChar() == ']')
                    return arr;
                backChar();
                while (true) {
                    var itemPtr = ptr + '/' + i;
                    arr.push(_parse(itemPtr));
                    whitespace();
                    var char = getChar();
                    if (char == ']')
                        break;
                    if (char != ',')
                        wasUnexpectedToken();
                    whitespace();
                    i++;
                }
                return arr;
            }
            function parseObject(ptr) {
                whitespace();
                var obj = {};
                if (getChar() == '}')
                    return obj;
                backChar();
                while (true) {
                    var loc = getLoc();
                    if (getChar() != '"')
                        wasUnexpectedToken();
                    var key = parseString();
                    var propPtr = ptr + '/' + escapeJsonPointer(key);
                    mapLoc(propPtr, 'key', loc);
                    map(propPtr, 'keyEnd');
                    whitespace();
                    if (getChar() != ':')
                        wasUnexpectedToken();
                    whitespace();
                    obj[key] = _parse(propPtr);
                    whitespace();
                    var char = getChar();
                    if (char == '}')
                        break;
                    if (char != ',')
                        wasUnexpectedToken();
                    whitespace();
                }
                return obj;
            }
            function read(str) {
                for (var i = 0; i < str.length; i++)
                    if (getChar() !== str[i])
                        wasUnexpectedToken();
            }
            function getChar() {
                checkUnexpectedEnd();
                var char = source[pos];
                pos++;
                column++;
                return char;
            }
            function backChar() {
                pos--;
                column--;
            }
            function getCharCode() {
                var count = 4;
                var code = 0;
                while (count--) {
                    code <<= 4;
                    var char = getChar().toLowerCase();
                    if (char >= 'a' && char <= 'f')
                        code += char.charCodeAt() - A_CODE + 10;
                    else if (char >= '0' && char <= '9')
                        code += +char;
                    else
                        wasUnexpectedToken();
                }
                return String.fromCharCode(code);
            }
            function getDigits() {
                var digits = '';
                while (source[pos] >= '0' && source[pos] <= '9')
                    digits += getChar();
                if (digits.length)
                    return digits;
                checkUnexpectedEnd();
                unexpectedToken();
            }
            function map(ptr, prop) {
                mapLoc(ptr, prop, getLoc());
            }
            function mapLoc(ptr, prop, loc) {
                pointers[ptr] = pointers[ptr] || {};
                pointers[ptr][prop] = loc;
            }
            function getLoc() {
                return {
                    line: line,
                    column: column,
                    pos: pos
                };
            }
            function unexpectedToken() {
                throw new SyntaxError('Unexpected token ' + source[pos] + ' in JSON at position ' + pos);
            }
            function wasUnexpectedToken() {
                backChar();
                unexpectedToken();
            }
            function checkUnexpectedEnd() {
                if (pos >= source.length)
                    throw new SyntaxError('Unexpected end of JSON input');
            }
        }
        ;
        exports.stringify = function(data, _, options) {
            if (!validType(data))
                return;
            var wsLine = 0;
            var wsPos, wsColumn;
            var whitespace = typeof options == 'object' ? options.space : options;
            switch (typeof whitespace) {
            case 'number':
                var len = whitespace > 10 ? 10 : whitespace < 0 ? 0 : Math.floor(whitespace);
                whitespace = len && repeat(len, ' ');
                wsPos = len;
                wsColumn = len;
                break;
            case 'string':
                whitespace = whitespace.slice(0, 10);
                wsPos = 0;
                wsColumn = 0;
                for (var j = 0; j < whitespace.length; j++) {
                    var char = whitespace[j];
                    switch (char) {
                    case ' ':
                        wsColumn++;
                        break;
                    case '\t':
                        wsColumn += 4;
                        break;
                    case '\r':
                        wsColumn = 0;
                        break;
                    case '\n':
                        wsColumn = 0;
                        wsLine++;
                        break;
                    default:
                        throw new Error('whitespace characters not allowed in JSON');
                    }
                    wsPos++;
                }
                break;
            default:
                whitespace = undefined;
            }
            var json = '';
            var pointers = {};
            var line = 0;
            var column = 0;
            var pos = 0;
            var es6 = options && options.es6 && typeof Map == 'function';
            _stringify(data, 0, '');
            return {
                json: json,
                pointers: pointers
            };
            function _stringify(_data, lvl, ptr) {
                map(ptr, 'value');
                switch (typeof _data) {
                case 'number':
                case 'bigint':
                case 'boolean':
                    out('' + _data);
                    break;
                case 'string':
                    out(quoted(_data));
                    break;
                case 'object':
                    if (_data === null) {
                        out('null');
                    } else if (typeof _data.toJSON == 'function') {
                        out(quoted(_data.toJSON()));
                    } else if (Array.isArray(_data)) {
                        stringifyArray();
                    } else if (es6) {
                        if (_data.constructor.BYTES_PER_ELEMENT)
                            stringifyArray();
                        else if (_data instanceof Map)
                            stringifyMapSet();
                        else if (_data instanceof Set)
                            stringifyMapSet(true);
                        else
                            stringifyObject();
                    } else {
                        stringifyObject();
                    }
                }
                map(ptr, 'valueEnd');
                function stringifyArray() {
                    if (_data.length) {
                        out('[');
                        var itemLvl = lvl + 1;
                        for (var i = 0; i < _data.length; i++) {
                            if (i)
                                out(',');
                            indent(itemLvl);
                            var item = validType(_data[i]) ? _data[i] : null;
                            var itemPtr = ptr + '/' + i;
                            _stringify(item, itemLvl, itemPtr);
                        }
                        indent(lvl);
                        out(']');
                    } else {
                        out('[]');
                    }
                }
                function stringifyObject() {
                    var keys = Object.keys(_data);
                    if (keys.length) {
                        out('{');
                        var propLvl = lvl + 1;
                        for (var i = 0; i < keys.length; i++) {
                            var key = keys[i];
                            var value = _data[key];
                            if (validType(value)) {
                                if (i)
                                    out(',');
                                var propPtr = ptr + '/' + escapeJsonPointer(key);
                                indent(propLvl);
                                map(propPtr, 'key');
                                out(quoted(key));
                                map(propPtr, 'keyEnd');
                                out(':');
                                if (whitespace)
                                    out(' ');
                                _stringify(value, propLvl, propPtr);
                            }
                        }
                        indent(lvl);
                        out('}');
                    } else {
                        out('{}');
                    }
                }
                function stringifyMapSet(isSet) {
                    if (_data.size) {
                        out('{');
                        var propLvl = lvl + 1;
                        var first = true;
                        var entries = _data.entries();
                        var entry = entries.next();
                        while (!entry.done) {
                            var item = entry.value;
                            var key = item[0];
                            var value = isSet ? true : item[1];
                            if (validType(value)) {
                                if (!first)
                                    out(',');
                                first = false;
                                var propPtr = ptr + '/' + escapeJsonPointer(key);
                                indent(propLvl);
                                map(propPtr, 'key');
                                out(quoted(key));
                                map(propPtr, 'keyEnd');
                                out(':');
                                if (whitespace)
                                    out(' ');
                                _stringify(value, propLvl, propPtr);
                            }
                            entry = entries.next();
                        }
                        indent(lvl);
                        out('}');
                    } else {
                        out('{}');
                    }
                }
            }
            function out(str) {
                column += str.length;
                pos += str.length;
                json += str;
            }
            function indent(lvl) {
                if (whitespace) {
                    json += '\n' + repeat(lvl, whitespace);
                    line++;
                    column = 0;
                    while (lvl--) {
                        if (wsLine) {
                            line += wsLine;
                            column = wsColumn;
                        } else {
                            column += wsColumn;
                        }
                        pos += wsPos;
                    }
                    pos += 1;
                }
            }
            function map(ptr, prop) {
                pointers[ptr] = pointers[ptr] || {};
                pointers[ptr][prop] = {
                    line: line,
                    column: column,
                    pos: pos
                };
            }
            function repeat(n, str) {
                return Array(n + 1).join(str);
            }
        }
        ;
        var VALID_TYPES = ['number', 'bigint', 'boolean', 'string', 'object'];
        function validType(data) {
            return VALID_TYPES.indexOf(typeof data) >= 0;
        }
        var ESC_QUOTE = /"|\\/g;
        var ESC_B = /[\b]/g;
        var ESC_F = /\f/g;
        var ESC_N = /\n/g;
        var ESC_R = /\r/g;
        var ESC_T = /\t/g;
        function quoted(str) {
            str = str.replace(ESC_QUOTE, '\\$&').replace(ESC_F, '\\f').replace(ESC_B, '\\b').replace(ESC_N, '\\n').replace(ESC_R, '\\r').replace(ESC_T, '\\t');
            return '"' + str + '"';
        }
        var ESC_0 = /~/g;
        var ESC_1 = /\//g;
        function escapeJsonPointer(str) {
            return str.replace(ESC_0, '~0').replace(ESC_1, '~1');
        }
    }
    ), (function(module, exports, __webpack_require__) {
        (function(exports) {
            "use strict";
            function isArray(obj) {
                if (obj !== null) {
                    return Object.prototype.toString.call(obj) === "[object Array]";
                } else {
                    return false;
                }
            }
            function isObject(obj) {
                if (obj !== null) {
                    return Object.prototype.toString.call(obj) === "[object Object]";
                } else {
                    return false;
                }
            }
            function strictDeepEqual(first, second) {
                if (first === second) {
                    return true;
                }
                var firstType = Object.prototype.toString.call(first);
                if (firstType !== Object.prototype.toString.call(second)) {
                    return false;
                }
                if (isArray(first) === true) {
                    if (first.length !== second.length) {
                        return false;
                    }
                    for (var i = 0; i < first.length; i++) {
                        if (strictDeepEqual(first[i], second[i]) === false) {
                            return false;
                        }
                    }
                    return true;
                }
                if (isObject(first) === true) {
                    var keysSeen = {};
                    for (var key in first) {
                        if (hasOwnProperty.call(first, key)) {
                            if (strictDeepEqual(first[key], second[key]) === false) {
                                return false;
                            }
                            keysSeen[key] = true;
                        }
                    }
                    for (var key2 in second) {
                        if (hasOwnProperty.call(second, key2)) {
                            if (keysSeen[key2] !== true) {
                                return false;
                            }
                        }
                    }
                    return true;
                }
                return false;
            }
            function isFalse(obj) {
                if (obj === "" || obj === false || obj === null) {
                    return true;
                } else if (isArray(obj) && obj.length === 0) {
                    return true;
                } else if (isObject(obj)) {
                    for (var key in obj) {
                        if (obj.hasOwnProperty(key)) {
                            return false;
                        }
                    }
                    return true;
                } else {
                    return false;
                }
            }
            function objValues(obj) {
                var keys = Object.keys(obj);
                var values = [];
                for (var i = 0; i < keys.length; i++) {
                    values.push(obj[keys[i]]);
                }
                return values;
            }
            function merge(a, b) {
                var merged = {};
                for (var key in a) {
                    merged[key] = a[key];
                }
                for (var key2 in b) {
                    merged[key2] = b[key2];
                }
                return merged;
            }
            var trimLeft;
            if (typeof String.prototype.trimLeft === "function") {
                trimLeft = function(str) {
                    return str.trimLeft();
                }
                ;
            } else {
                trimLeft = function(str) {
                    return str.match(/^\s*(.*)/)[1];
                }
                ;
            }
            var TYPE_NUMBER = 0;
            var TYPE_ANY = 1;
            var TYPE_STRING = 2;
            var TYPE_ARRAY = 3;
            var TYPE_OBJECT = 4;
            var TYPE_BOOLEAN = 5;
            var TYPE_EXPREF = 6;
            var TYPE_NULL = 7;
            var TYPE_ARRAY_NUMBER = 8;
            var TYPE_ARRAY_STRING = 9;
            var TOK_EOF = "EOF";
            var TOK_UNQUOTEDIDENTIFIER = "UnquotedIdentifier";
            var TOK_QUOTEDIDENTIFIER = "QuotedIdentifier";
            var TOK_RBRACKET = "Rbracket";
            var TOK_RPAREN = "Rparen";
            var TOK_COMMA = "Comma";
            var TOK_COLON = "Colon";
            var TOK_RBRACE = "Rbrace";
            var TOK_NUMBER = "Number";
            var TOK_CURRENT = "Current";
            var TOK_EXPREF = "Expref";
            var TOK_PIPE = "Pipe";
            var TOK_OR = "Or";
            var TOK_AND = "And";
            var TOK_EQ = "EQ";
            var TOK_GT = "GT";
            var TOK_LT = "LT";
            var TOK_GTE = "GTE";
            var TOK_LTE = "LTE";
            var TOK_NE = "NE";
            var TOK_FLATTEN = "Flatten";
            var TOK_STAR = "Star";
            var TOK_FILTER = "Filter";
            var TOK_DOT = "Dot";
            var TOK_NOT = "Not";
            var TOK_LBRACE = "Lbrace";
            var TOK_LBRACKET = "Lbracket";
            var TOK_LPAREN = "Lparen";
            var TOK_LITERAL = "Literal";
            var basicTokens = {
                ".": TOK_DOT,
                "*": TOK_STAR,
                ",": TOK_COMMA,
                ":": TOK_COLON,
                "{": TOK_LBRACE,
                "}": TOK_RBRACE,
                "]": TOK_RBRACKET,
                "(": TOK_LPAREN,
                ")": TOK_RPAREN,
                "@": TOK_CURRENT
            };
            var operatorStartToken = {
                "<": true,
                ">": true,
                "=": true,
                "!": true
            };
            var skipChars = {
                " ": true,
                "\t": true,
                "\n": true
            };
            function isAlpha(ch) {
                return (ch >= "a" && ch <= "z") || (ch >= "A" && ch <= "Z") || ch === "_";
            }
            function isNum(ch) {
                return (ch >= "0" && ch <= "9") || ch === "-";
            }
            function isAlphaNum(ch) {
                return (ch >= "a" && ch <= "z") || (ch >= "A" && ch <= "Z") || (ch >= "0" && ch <= "9") || ch === "_";
            }
            function Lexer() {}
            Lexer.prototype = {
                tokenize: function(stream) {
                    var tokens = [];
                    this._current = 0;
                    var start;
                    var identifier;
                    var token;
                    while (this._current < stream.length) {
                        if (isAlpha(stream[this._current])) {
                            start = this._current;
                            identifier = this._consumeUnquotedIdentifier(stream);
                            tokens.push({
                                type: TOK_UNQUOTEDIDENTIFIER,
                                value: identifier,
                                start: start
                            });
                        } else if (basicTokens[stream[this._current]] !== undefined) {
                            tokens.push({
                                type: basicTokens[stream[this._current]],
                                value: stream[this._current],
                                start: this._current
                            });
                            this._current++;
                        } else if (isNum(stream[this._current])) {
                            token = this._consumeNumber(stream);
                            tokens.push(token);
                        } else if (stream[this._current] === "[") {
                            token = this._consumeLBracket(stream);
                            tokens.push(token);
                        } else if (stream[this._current] === "\"") {
                            start = this._current;
                            identifier = this._consumeQuotedIdentifier(stream);
                            tokens.push({
                                type: TOK_QUOTEDIDENTIFIER,
                                value: identifier,
                                start: start
                            });
                        } else if (stream[this._current] === "'") {
                            start = this._current;
                            identifier = this._consumeRawStringLiteral(stream);
                            tokens.push({
                                type: TOK_LITERAL,
                                value: identifier,
                                start: start
                            });
                        } else if (stream[this._current] === "`") {
                            start = this._current;
                            var literal = this._consumeLiteral(stream);
                            tokens.push({
                                type: TOK_LITERAL,
                                value: literal,
                                start: start
                            });
                        } else if (operatorStartToken[stream[this._current]] !== undefined) {
                            tokens.push(this._consumeOperator(stream));
                        } else if (skipChars[stream[this._current]] !== undefined) {
                            this._current++;
                        } else if (stream[this._current] === "&") {
                            start = this._current;
                            this._current++;
                            if (stream[this._current] === "&") {
                                this._current++;
                                tokens.push({
                                    type: TOK_AND,
                                    value: "&&",
                                    start: start
                                });
                            } else {
                                tokens.push({
                                    type: TOK_EXPREF,
                                    value: "&",
                                    start: start
                                });
                            }
                        } else if (stream[this._current] === "|") {
                            start = this._current;
                            this._current++;
                            if (stream[this._current] === "|") {
                                this._current++;
                                tokens.push({
                                    type: TOK_OR,
                                    value: "||",
                                    start: start
                                });
                            } else {
                                tokens.push({
                                    type: TOK_PIPE,
                                    value: "|",
                                    start: start
                                });
                            }
                        } else {
                            var error = new Error("Unknown character:" + stream[this._current]);
                            error.name = "LexerError";
                            throw error;
                        }
                    }
                    return tokens;
                },
                _consumeUnquotedIdentifier: function(stream) {
                    var start = this._current;
                    this._current++;
                    while (this._current < stream.length && isAlphaNum(stream[this._current])) {
                        this._current++;
                    }
                    return stream.slice(start, this._current);
                },
                _consumeQuotedIdentifier: function(stream) {
                    var start = this._current;
                    this._current++;
                    var maxLength = stream.length;
                    while (stream[this._current] !== "\"" && this._current < maxLength) {
                        var current = this._current;
                        if (stream[current] === "\\" && (stream[current + 1] === "\\" || stream[current + 1] === "\"")) {
                            current += 2;
                        } else {
                            current++;
                        }
                        this._current = current;
                    }
                    this._current++;
                    return JSON.parse(stream.slice(start, this._current));
                },
                _consumeRawStringLiteral: function(stream) {
                    var start = this._current;
                    this._current++;
                    var maxLength = stream.length;
                    while (stream[this._current] !== "'" && this._current < maxLength) {
                        var current = this._current;
                        if (stream[current] === "\\" && (stream[current + 1] === "\\" || stream[current + 1] === "'")) {
                            current += 2;
                        } else {
                            current++;
                        }
                        this._current = current;
                    }
                    this._current++;
                    var literal = stream.slice(start + 1, this._current - 1);
                    return literal.replace("\\'", "'");
                },
                _consumeNumber: function(stream) {
                    var start = this._current;
                    this._current++;
                    var maxLength = stream.length;
                    while (isNum(stream[this._current]) && this._current < maxLength) {
                        this._current++;
                    }
                    var value = parseInt(stream.slice(start, this._current));
                    return {
                        type: TOK_NUMBER,
                        value: value,
                        start: start
                    };
                },
                _consumeLBracket: function(stream) {
                    var start = this._current;
                    this._current++;
                    if (stream[this._current] === "?") {
                        this._current++;
                        return {
                            type: TOK_FILTER,
                            value: "[?",
                            start: start
                        };
                    } else if (stream[this._current] === "]") {
                        this._current++;
                        return {
                            type: TOK_FLATTEN,
                            value: "[]",
                            start: start
                        };
                    } else {
                        return {
                            type: TOK_LBRACKET,
                            value: "[",
                            start: start
                        };
                    }
                },
                _consumeOperator: function(stream) {
                    var start = this._current;
                    var startingChar = stream[start];
                    this._current++;
                    if (startingChar === "!") {
                        if (stream[this._current] === "=") {
                            this._current++;
                            return {
                                type: TOK_NE,
                                value: "!=",
                                start: start
                            };
                        } else {
                            return {
                                type: TOK_NOT,
                                value: "!",
                                start: start
                            };
                        }
                    } else if (startingChar === "<") {
                        if (stream[this._current] === "=") {
                            this._current++;
                            return {
                                type: TOK_LTE,
                                value: "<=",
                                start: start
                            };
                        } else {
                            return {
                                type: TOK_LT,
                                value: "<",
                                start: start
                            };
                        }
                    } else if (startingChar === ">") {
                        if (stream[this._current] === "=") {
                            this._current++;
                            return {
                                type: TOK_GTE,
                                value: ">=",
                                start: start
                            };
                        } else {
                            return {
                                type: TOK_GT,
                                value: ">",
                                start: start
                            };
                        }
                    } else if (startingChar === "=") {
                        if (stream[this._current] === "=") {
                            this._current++;
                            return {
                                type: TOK_EQ,
                                value: "==",
                                start: start
                            };
                        }
                    }
                },
                _consumeLiteral: function(stream) {
                    this._current++;
                    var start = this._current;
                    var maxLength = stream.length;
                    var literal;
                    while (stream[this._current] !== "`" && this._current < maxLength) {
                        var current = this._current;
                        if (stream[current] === "\\" && (stream[current + 1] === "\\" || stream[current + 1] === "`")) {
                            current += 2;
                        } else {
                            current++;
                        }
                        this._current = current;
                    }
                    var literalString = trimLeft(stream.slice(start, this._current));
                    literalString = literalString.replace("\\`", "`");
                    if (this._looksLikeJSON(literalString)) {
                        literal = JSON.parse(literalString);
                    } else {
                        literal = JSON.parse("\"" + literalString + "\"");
                    }
                    this._current++;
                    return literal;
                },
                _looksLikeJSON: function(literalString) {
                    var startingChars = "[{\"";
                    var jsonLiterals = ["true", "false", "null"];
                    var numberLooking = "-0123456789";
                    if (literalString === "") {
                        return false;
                    } else if (startingChars.indexOf(literalString[0]) >= 0) {
                        return true;
                    } else if (jsonLiterals.indexOf(literalString) >= 0) {
                        return true;
                    } else if (numberLooking.indexOf(literalString[0]) >= 0) {
                        try {
                            JSON.parse(literalString);
                            return true;
                        } catch (ex) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
            };
            var bindingPower = {};
            bindingPower[TOK_EOF] = 0;
            bindingPower[TOK_UNQUOTEDIDENTIFIER] = 0;
            bindingPower[TOK_QUOTEDIDENTIFIER] = 0;
            bindingPower[TOK_RBRACKET] = 0;
            bindingPower[TOK_RPAREN] = 0;
            bindingPower[TOK_COMMA] = 0;
            bindingPower[TOK_RBRACE] = 0;
            bindingPower[TOK_NUMBER] = 0;
            bindingPower[TOK_CURRENT] = 0;
            bindingPower[TOK_EXPREF] = 0;
            bindingPower[TOK_PIPE] = 1;
            bindingPower[TOK_OR] = 2;
            bindingPower[TOK_AND] = 3;
            bindingPower[TOK_EQ] = 5;
            bindingPower[TOK_GT] = 5;
            bindingPower[TOK_LT] = 5;
            bindingPower[TOK_GTE] = 5;
            bindingPower[TOK_LTE] = 5;
            bindingPower[TOK_NE] = 5;
            bindingPower[TOK_FLATTEN] = 9;
            bindingPower[TOK_STAR] = 20;
            bindingPower[TOK_FILTER] = 21;
            bindingPower[TOK_DOT] = 40;
            bindingPower[TOK_NOT] = 45;
            bindingPower[TOK_LBRACE] = 50;
            bindingPower[TOK_LBRACKET] = 55;
            bindingPower[TOK_LPAREN] = 60;
            function Parser() {}
            Parser.prototype = {
                parse: function(expression) {
                    this._loadTokens(expression);
                    this.index = 0;
                    var ast = this.expression(0);
                    if (this._lookahead(0) !== TOK_EOF) {
                        var t = this._lookaheadToken(0);
                        var error = new Error("Unexpected token type: " + t.type + ", value: " + t.value);
                        error.name = "ParserError";
                        throw error;
                    }
                    return ast;
                },
                _loadTokens: function(expression) {
                    var lexer = new Lexer();
                    var tokens = lexer.tokenize(expression);
                    tokens.push({
                        type: TOK_EOF,
                        value: "",
                        start: expression.length
                    });
                    this.tokens = tokens;
                },
                expression: function(rbp) {
                    var leftToken = this._lookaheadToken(0);
                    this._advance();
                    var left = this.nud(leftToken);
                    var currentToken = this._lookahead(0);
                    while (rbp < bindingPower[currentToken]) {
                        this._advance();
                        left = this.led(currentToken, left);
                        currentToken = this._lookahead(0);
                    }
                    return left;
                },
                _lookahead: function(number) {
                    return this.tokens[this.index + number].type;
                },
                _lookaheadToken: function(number) {
                    return this.tokens[this.index + number];
                },
                _advance: function() {
                    this.index++;
                },
                nud: function(token) {
                    var left;
                    var right;
                    var expression;
                    switch (token.type) {
                    case TOK_LITERAL:
                        return {
                            type: "Literal",
                            value: token.value
                        };
                    case TOK_UNQUOTEDIDENTIFIER:
                        return {
                            type: "Field",
                            name: token.value
                        };
                    case TOK_QUOTEDIDENTIFIER:
                        var node = {
                            type: "Field",
                            name: token.value
                        };
                        if (this._lookahead(0) === TOK_LPAREN) {
                            throw new Error("Quoted identifier not allowed for function names.");
                        } else {
                            return node;
                        }
                        break;
                    case TOK_NOT:
                        right = this.expression(bindingPower.Not);
                        return {
                            type: "NotExpression",
                            children: [right]
                        };
                    case TOK_STAR:
                        left = {
                            type: "Identity"
                        };
                        right = null;
                        if (this._lookahead(0) === TOK_RBRACKET) {
                            right = {
                                type: "Identity"
                            };
                        } else {
                            right = this._parseProjectionRHS(bindingPower.Star);
                        }
                        return {
                            type: "ValueProjection",
                            children: [left, right]
                        };
                    case TOK_FILTER:
                        return this.led(token.type, {
                            type: "Identity"
                        });
                    case TOK_LBRACE:
                        return this._parseMultiselectHash();
                    case TOK_FLATTEN:
                        left = {
                            type: TOK_FLATTEN,
                            children: [{
                                type: "Identity"
                            }]
                        };
                        right = this._parseProjectionRHS(bindingPower.Flatten);
                        return {
                            type: "Projection",
                            children: [left, right]
                        };
                    case TOK_LBRACKET:
                        if (this._lookahead(0) === TOK_NUMBER || this._lookahead(0) === TOK_COLON) {
                            right = this._parseIndexExpression();
                            return this._projectIfSlice({
                                type: "Identity"
                            }, right);
                        } else if (this._lookahead(0) === TOK_STAR && this._lookahead(1) === TOK_RBRACKET) {
                            this._advance();
                            this._advance();
                            right = this._parseProjectionRHS(bindingPower.Star);
                            return {
                                type: "Projection",
                                children: [{
                                    type: "Identity"
                                }, right]
                            };
                        } else {
                            return this._parseMultiselectList();
                        }
                        break;
                    case TOK_CURRENT:
                        return {
                            type: TOK_CURRENT
                        };
                    case TOK_EXPREF:
                        expression = this.expression(bindingPower.Expref);
                        return {
                            type: "ExpressionReference",
                            children: [expression]
                        };
                    case TOK_LPAREN:
                        var args = [];
                        while (this._lookahead(0) !== TOK_RPAREN) {
                            if (this._lookahead(0) === TOK_CURRENT) {
                                expression = {
                                    type: TOK_CURRENT
                                };
                                this._advance();
                            } else {
                                expression = this.expression(0);
                            }
                            args.push(expression);
                        }
                        this._match(TOK_RPAREN);
                        return args[0];
                    default:
                        this._errorToken(token);
                    }
                },
                led: function(tokenName, left) {
                    var right;
                    switch (tokenName) {
                    case TOK_DOT:
                        var rbp = bindingPower.Dot;
                        if (this._lookahead(0) !== TOK_STAR) {
                            right = this._parseDotRHS(rbp);
                            return {
                                type: "Subexpression",
                                children: [left, right]
                            };
                        } else {
                            this._advance();
                            right = this._parseProjectionRHS(rbp);
                            return {
                                type: "ValueProjection",
                                children: [left, right]
                            };
                        }
                        break;
                    case TOK_PIPE:
                        right = this.expression(bindingPower.Pipe);
                        return {
                            type: TOK_PIPE,
                            children: [left, right]
                        };
                    case TOK_OR:
                        right = this.expression(bindingPower.Or);
                        return {
                            type: "OrExpression",
                            children: [left, right]
                        };
                    case TOK_AND:
                        right = this.expression(bindingPower.And);
                        return {
                            type: "AndExpression",
                            children: [left, right]
                        };
                    case TOK_LPAREN:
                        var name = left.name;
                        var args = [];
                        var expression, node;
                        while (this._lookahead(0) !== TOK_RPAREN) {
                            if (this._lookahead(0) === TOK_CURRENT) {
                                expression = {
                                    type: TOK_CURRENT
                                };
                                this._advance();
                            } else {
                                expression = this.expression(0);
                            }
                            if (this._lookahead(0) === TOK_COMMA) {
                                this._match(TOK_COMMA);
                            }
                            args.push(expression);
                        }
                        this._match(TOK_RPAREN);
                        node = {
                            type: "Function",
                            name: name,
                            children: args
                        };
                        return node;
                    case TOK_FILTER:
                        var condition = this.expression(0);
                        this._match(TOK_RBRACKET);
                        if (this._lookahead(0) === TOK_FLATTEN) {
                            right = {
                                type: "Identity"
                            };
                        } else {
                            right = this._parseProjectionRHS(bindingPower.Filter);
                        }
                        return {
                            type: "FilterProjection",
                            children: [left, right, condition]
                        };
                    case TOK_FLATTEN:
                        var leftNode = {
                            type: TOK_FLATTEN,
                            children: [left]
                        };
                        var rightNode = this._parseProjectionRHS(bindingPower.Flatten);
                        return {
                            type: "Projection",
                            children: [leftNode, rightNode]
                        };
                    case TOK_EQ:
                    case TOK_NE:
                    case TOK_GT:
                    case TOK_GTE:
                    case TOK_LT:
                    case TOK_LTE:
                        return this._parseComparator(left, tokenName);
                    case TOK_LBRACKET:
                        var token = this._lookaheadToken(0);
                        if (token.type === TOK_NUMBER || token.type === TOK_COLON) {
                            right = this._parseIndexExpression();
                            return this._projectIfSlice(left, right);
                        } else {
                            this._match(TOK_STAR);
                            this._match(TOK_RBRACKET);
                            right = this._parseProjectionRHS(bindingPower.Star);
                            return {
                                type: "Projection",
                                children: [left, right]
                            };
                        }
                        break;
                    default:
                        this._errorToken(this._lookaheadToken(0));
                    }
                },
                _match: function(tokenType) {
                    if (this._lookahead(0) === tokenType) {
                        this._advance();
                    } else {
                        var t = this._lookaheadToken(0);
                        var error = new Error("Expected " + tokenType + ", got: " + t.type);
                        error.name = "ParserError";
                        throw error;
                    }
                },
                _errorToken: function(token) {
                    var error = new Error("Invalid token (" + token.type + "): \"" + token.value + "\"");
                    error.name = "ParserError";
                    throw error;
                },
                _parseIndexExpression: function() {
                    if (this._lookahead(0) === TOK_COLON || this._lookahead(1) === TOK_COLON) {
                        return this._parseSliceExpression();
                    } else {
                        var node = {
                            type: "Index",
                            value: this._lookaheadToken(0).value
                        };
                        this._advance();
                        this._match(TOK_RBRACKET);
                        return node;
                    }
                },
                _projectIfSlice: function(left, right) {
                    var indexExpr = {
                        type: "IndexExpression",
                        children: [left, right]
                    };
                    if (right.type === "Slice") {
                        return {
                            type: "Projection",
                            children: [indexExpr, this._parseProjectionRHS(bindingPower.Star)]
                        };
                    } else {
                        return indexExpr;
                    }
                },
                _parseSliceExpression: function() {
                    var parts = [null, null, null];
                    var index = 0;
                    var currentToken = this._lookahead(0);
                    while (currentToken !== TOK_RBRACKET && index < 3) {
                        if (currentToken === TOK_COLON) {
                            index++;
                            this._advance();
                        } else if (currentToken === TOK_NUMBER) {
                            parts[index] = this._lookaheadToken(0).value;
                            this._advance();
                        } else {
                            var t = this._lookahead(0);
                            var error = new Error("Syntax error, unexpected token: " + t.value + "(" + t.type + ")");
                            error.name = "Parsererror";
                            throw error;
                        }
                        currentToken = this._lookahead(0);
                    }
                    this._match(TOK_RBRACKET);
                    return {
                        type: "Slice",
                        children: parts
                    };
                },
                _parseComparator: function(left, comparator) {
                    var right = this.expression(bindingPower[comparator]);
                    return {
                        type: "Comparator",
                        name: comparator,
                        children: [left, right]
                    };
                },
                _parseDotRHS: function(rbp) {
                    var lookahead = this._lookahead(0);
                    var exprTokens = [TOK_UNQUOTEDIDENTIFIER, TOK_QUOTEDIDENTIFIER, TOK_STAR];
                    if (exprTokens.indexOf(lookahead) >= 0) {
                        return this.expression(rbp);
                    } else if (lookahead === TOK_LBRACKET) {
                        this._match(TOK_LBRACKET);
                        return this._parseMultiselectList();
                    } else if (lookahead === TOK_LBRACE) {
                        this._match(TOK_LBRACE);
                        return this._parseMultiselectHash();
                    }
                },
                _parseProjectionRHS: function(rbp) {
                    var right;
                    if (bindingPower[this._lookahead(0)] < 10) {
                        right = {
                            type: "Identity"
                        };
                    } else if (this._lookahead(0) === TOK_LBRACKET) {
                        right = this.expression(rbp);
                    } else if (this._lookahead(0) === TOK_FILTER) {
                        right = this.expression(rbp);
                    } else if (this._lookahead(0) === TOK_DOT) {
                        this._match(TOK_DOT);
                        right = this._parseDotRHS(rbp);
                    } else {
                        var t = this._lookaheadToken(0);
                        var error = new Error("Sytanx error, unexpected token: " + t.value + "(" + t.type + ")");
                        error.name = "ParserError";
                        throw error;
                    }
                    return right;
                },
                _parseMultiselectList: function() {
                    var expressions = [];
                    while (this._lookahead(0) !== TOK_RBRACKET) {
                        var expression = this.expression(0);
                        expressions.push(expression);
                        if (this._lookahead(0) === TOK_COMMA) {
                            this._match(TOK_COMMA);
                            if (this._lookahead(0) === TOK_RBRACKET) {
                                throw new Error("Unexpected token Rbracket");
                            }
                        }
                    }
                    this._match(TOK_RBRACKET);
                    return {
                        type: "MultiSelectList",
                        children: expressions
                    };
                },
                _parseMultiselectHash: function() {
                    var pairs = [];
                    var identifierTypes = [TOK_UNQUOTEDIDENTIFIER, TOK_QUOTEDIDENTIFIER];
                    var keyToken, keyName, value, node;
                    for (; ; ) {
                        keyToken = this._lookaheadToken(0);
                        if (identifierTypes.indexOf(keyToken.type) < 0) {
                            throw new Error("Expecting an identifier token, got: " + keyToken.type);
                        }
                        keyName = keyToken.value;
                        this._advance();
                        this._match(TOK_COLON);
                        value = this.expression(0);
                        node = {
                            type: "KeyValuePair",
                            name: keyName,
                            value: value
                        };
                        pairs.push(node);
                        if (this._lookahead(0) === TOK_COMMA) {
                            this._match(TOK_COMMA);
                        } else if (this._lookahead(0) === TOK_RBRACE) {
                            this._match(TOK_RBRACE);
                            break;
                        }
                    }
                    return {
                        type: "MultiSelectHash",
                        children: pairs
                    };
                }
            };
            function TreeInterpreter(runtime) {
                this.runtime = runtime;
            }
            TreeInterpreter.prototype = {
                search: function(node, value) {
                    return this.visit(node, value);
                },
                visit: function(node, value) {
                    var matched, current, result, first, second, field, left, right, collected, i;
                    switch (node.type) {
                    case "Field":
                        if (value === null) {
                            return null;
                        } else if (isObject(value)) {
                            field = value[node.name];
                            if (field === undefined) {
                                return null;
                            } else {
                                return field;
                            }
                        } else {
                            return null;
                        }
                        break;
                    case "Subexpression":
                        result = this.visit(node.children[0], value);
                        for (i = 1; i < node.children.length; i++) {
                            result = this.visit(node.children[1], result);
                            if (result === null) {
                                return null;
                            }
                        }
                        return result;
                    case "IndexExpression":
                        left = this.visit(node.children[0], value);
                        right = this.visit(node.children[1], left);
                        return right;
                    case "Index":
                        if (!isArray(value)) {
                            return null;
                        }
                        var index = node.value;
                        if (index < 0) {
                            index = value.length + index;
                        }
                        result = value[index];
                        if (result === undefined) {
                            result = null;
                        }
                        return result;
                    case "Slice":
                        if (!isArray(value)) {
                            return null;
                        }
                        var sliceParams = node.children.slice(0);
                        var computed = this.computeSliceParams(value.length, sliceParams);
                        var start = computed[0];
                        var stop = computed[1];
                        var step = computed[2];
                        result = [];
                        if (step > 0) {
                            for (i = start; i < stop; i += step) {
                                result.push(value[i]);
                            }
                        } else {
                            for (i = start; i > stop; i += step) {
                                result.push(value[i]);
                            }
                        }
                        return result;
                    case "Projection":
                        var base = this.visit(node.children[0], value);
                        if (!isArray(base)) {
                            return null;
                        }
                        collected = [];
                        for (i = 0; i < base.length; i++) {
                            current = this.visit(node.children[1], base[i]);
                            if (current !== null) {
                                collected.push(current);
                            }
                        }
                        return collected;
                    case "ValueProjection":
                        base = this.visit(node.children[0], value);
                        if (!isObject(base)) {
                            return null;
                        }
                        collected = [];
                        var values = objValues(base);
                        for (i = 0; i < values.length; i++) {
                            current = this.visit(node.children[1], values[i]);
                            if (current !== null) {
                                collected.push(current);
                            }
                        }
                        return collected;
                    case "FilterProjection":
                        base = this.visit(node.children[0], value);
                        if (!isArray(base)) {
                            return null;
                        }
                        var filtered = [];
                        var finalResults = [];
                        for (i = 0; i < base.length; i++) {
                            matched = this.visit(node.children[2], base[i]);
                            if (!isFalse(matched)) {
                                filtered.push(base[i]);
                            }
                        }
                        for (var j = 0; j < filtered.length; j++) {
                            current = this.visit(node.children[1], filtered[j]);
                            if (current !== null) {
                                finalResults.push(current);
                            }
                        }
                        return finalResults;
                    case "Comparator":
                        first = this.visit(node.children[0], value);
                        second = this.visit(node.children[1], value);
                        switch (node.name) {
                        case TOK_EQ:
                            result = strictDeepEqual(first, second);
                            break;
                        case TOK_NE:
                            result = !strictDeepEqual(first, second);
                            break;
                        case TOK_GT:
                            result = first > second;
                            break;
                        case TOK_GTE:
                            result = first >= second;
                            break;
                        case TOK_LT:
                            result = first < second;
                            break;
                        case TOK_LTE:
                            result = first <= second;
                            break;
                        default:
                            throw new Error("Unknown comparator: " + node.name);
                        }
                        return result;
                    case TOK_FLATTEN:
                        var original = this.visit(node.children[0], value);
                        if (!isArray(original)) {
                            return null;
                        }
                        var merged = [];
                        for (i = 0; i < original.length; i++) {
                            current = original[i];
                            if (isArray(current)) {
                                merged.push.apply(merged, current);
                            } else {
                                merged.push(current);
                            }
                        }
                        return merged;
                    case "Identity":
                        return value;
                    case "MultiSelectList":
                        if (value === null) {
                            return null;
                        }
                        collected = [];
                        for (i = 0; i < node.children.length; i++) {
                            collected.push(this.visit(node.children[i], value));
                        }
                        return collected;
                    case "MultiSelectHash":
                        if (value === null) {
                            return null;
                        }
                        collected = {};
                        var child;
                        for (i = 0; i < node.children.length; i++) {
                            child = node.children[i];
                            collected[child.name] = this.visit(child.value, value);
                        }
                        return collected;
                    case "OrExpression":
                        matched = this.visit(node.children[0], value);
                        if (isFalse(matched)) {
                            matched = this.visit(node.children[1], value);
                        }
                        return matched;
                    case "AndExpression":
                        first = this.visit(node.children[0], value);
                        if (isFalse(first) === true) {
                            return first;
                        }
                        return this.visit(node.children[1], value);
                    case "NotExpression":
                        first = this.visit(node.children[0], value);
                        return isFalse(first);
                    case "Literal":
                        return node.value;
                    case TOK_PIPE:
                        left = this.visit(node.children[0], value);
                        return this.visit(node.children[1], left);
                    case TOK_CURRENT:
                        return value;
                    case "Function":
                        var resolvedArgs = [];
                        for (i = 0; i < node.children.length; i++) {
                            resolvedArgs.push(this.visit(node.children[i], value));
                        }
                        return this.runtime.callFunction(node.name, resolvedArgs);
                    case "ExpressionReference":
                        var refNode = node.children[0];
                        refNode.jmespathType = TOK_EXPREF;
                        return refNode;
                    default:
                        throw new Error("Unknown node type: " + node.type);
                    }
                },
                computeSliceParams: function(arrayLength, sliceParams) {
                    var start = sliceParams[0];
                    var stop = sliceParams[1];
                    var step = sliceParams[2];
                    var computed = [null, null, null];
                    if (step === null) {
                        step = 1;
                    } else if (step === 0) {
                        var error = new Error("Invalid slice, step cannot be 0");
                        error.name = "RuntimeError";
                        throw error;
                    }
                    var stepValueNegative = step < 0 ? true : false;
                    if (start === null) {
                        start = stepValueNegative ? arrayLength - 1 : 0;
                    } else {
                        start = this.capSliceRange(arrayLength, start, step);
                    }
                    if (stop === null) {
                        stop = stepValueNegative ? -1 : arrayLength;
                    } else {
                        stop = this.capSliceRange(arrayLength, stop, step);
                    }
                    computed[0] = start;
                    computed[1] = stop;
                    computed[2] = step;
                    return computed;
                },
                capSliceRange: function(arrayLength, actualValue, step) {
                    if (actualValue < 0) {
                        actualValue += arrayLength;
                        if (actualValue < 0) {
                            actualValue = step < 0 ? -1 : 0;
                        }
                    } else if (actualValue >= arrayLength) {
                        actualValue = step < 0 ? arrayLength - 1 : arrayLength;
                    }
                    return actualValue;
                }
            };
            function Runtime(interpreter) {
                this._interpreter = interpreter;
                this.functionTable = {
                    abs: {
                        _func: this._functionAbs,
                        _signature: [{
                            types: [TYPE_NUMBER]
                        }]
                    },
                    avg: {
                        _func: this._functionAvg,
                        _signature: [{
                            types: [TYPE_ARRAY_NUMBER]
                        }]
                    },
                    ceil: {
                        _func: this._functionCeil,
                        _signature: [{
                            types: [TYPE_NUMBER]
                        }]
                    },
                    contains: {
                        _func: this._functionContains,
                        _signature: [{
                            types: [TYPE_STRING, TYPE_ARRAY]
                        }, {
                            types: [TYPE_ANY]
                        }]
                    },
                    "ends_with": {
                        _func: this._functionEndsWith,
                        _signature: [{
                            types: [TYPE_STRING]
                        }, {
                            types: [TYPE_STRING]
                        }]
                    },
                    floor: {
                        _func: this._functionFloor,
                        _signature: [{
                            types: [TYPE_NUMBER]
                        }]
                    },
                    length: {
                        _func: this._functionLength,
                        _signature: [{
                            types: [TYPE_STRING, TYPE_ARRAY, TYPE_OBJECT]
                        }]
                    },
                    map: {
                        _func: this._functionMap,
                        _signature: [{
                            types: [TYPE_EXPREF]
                        }, {
                            types: [TYPE_ARRAY]
                        }]
                    },
                    max: {
                        _func: this._functionMax,
                        _signature: [{
                            types: [TYPE_ARRAY_NUMBER, TYPE_ARRAY_STRING]
                        }]
                    },
                    "merge": {
                        _func: this._functionMerge,
                        _signature: [{
                            types: [TYPE_OBJECT],
                            variadic: true
                        }]
                    },
                    "max_by": {
                        _func: this._functionMaxBy,
                        _signature: [{
                            types: [TYPE_ARRAY]
                        }, {
                            types: [TYPE_EXPREF]
                        }]
                    },
                    sum: {
                        _func: this._functionSum,
                        _signature: [{
                            types: [TYPE_ARRAY_NUMBER]
                        }]
                    },
                    "starts_with": {
                        _func: this._functionStartsWith,
                        _signature: [{
                            types: [TYPE_STRING]
                        }, {
                            types: [TYPE_STRING]
                        }]
                    },
                    min: {
                        _func: this._functionMin,
                        _signature: [{
                            types: [TYPE_ARRAY_NUMBER, TYPE_ARRAY_STRING]
                        }]
                    },
                    "min_by": {
                        _func: this._functionMinBy,
                        _signature: [{
                            types: [TYPE_ARRAY]
                        }, {
                            types: [TYPE_EXPREF]
                        }]
                    },
                    type: {
                        _func: this._functionType,
                        _signature: [{
                            types: [TYPE_ANY]
                        }]
                    },
                    keys: {
                        _func: this._functionKeys,
                        _signature: [{
                            types: [TYPE_OBJECT]
                        }]
                    },
                    values: {
                        _func: this._functionValues,
                        _signature: [{
                            types: [TYPE_OBJECT]
                        }]
                    },
                    sort: {
                        _func: this._functionSort,
                        _signature: [{
                            types: [TYPE_ARRAY_STRING, TYPE_ARRAY_NUMBER]
                        }]
                    },
                    "sort_by": {
                        _func: this._functionSortBy,
                        _signature: [{
                            types: [TYPE_ARRAY]
                        }, {
                            types: [TYPE_EXPREF]
                        }]
                    },
                    join: {
                        _func: this._functionJoin,
                        _signature: [{
                            types: [TYPE_STRING]
                        }, {
                            types: [TYPE_ARRAY_STRING]
                        }]
                    },
                    reverse: {
                        _func: this._functionReverse,
                        _signature: [{
                            types: [TYPE_STRING, TYPE_ARRAY]
                        }]
                    },
                    "to_array": {
                        _func: this._functionToArray,
                        _signature: [{
                            types: [TYPE_ANY]
                        }]
                    },
                    "to_string": {
                        _func: this._functionToString,
                        _signature: [{
                            types: [TYPE_ANY]
                        }]
                    },
                    "to_number": {
                        _func: this._functionToNumber,
                        _signature: [{
                            types: [TYPE_ANY]
                        }]
                    },
                    "not_null": {
                        _func: this._functionNotNull,
                        _signature: [{
                            types: [TYPE_ANY],
                            variadic: true
                        }]
                    }
                };
            }
            Runtime.prototype = {
                callFunction: function(name, resolvedArgs) {
                    var functionEntry = this.functionTable[name];
                    if (functionEntry === undefined) {
                        throw new Error("Unknown function: " + name + "()");
                    }
                    this._validateArgs(name, resolvedArgs, functionEntry._signature);
                    return functionEntry._func.call(this, resolvedArgs);
                },
                _validateArgs: function(name, args, signature) {
                    var pluralized;
                    if (signature[signature.length - 1].variadic) {
                        if (args.length < signature.length) {
                            pluralized = signature.length === 1 ? " argument" : " arguments";
                            throw new Error("ArgumentError: " + name + "() " + "takes at least" + signature.length + pluralized + " but received " + args.length);
                        }
                    } else if (args.length !== signature.length) {
                        pluralized = signature.length === 1 ? " argument" : " arguments";
                        throw new Error("ArgumentError: " + name + "() " + "takes " + signature.length + pluralized + " but received " + args.length);
                    }
                    var currentSpec;
                    var actualType;
                    var typeMatched;
                    for (var i = 0; i < signature.length; i++) {
                        typeMatched = false;
                        currentSpec = signature[i].types;
                        actualType = this._getTypeName(args[i]);
                        for (var j = 0; j < currentSpec.length; j++) {
                            if (this._typeMatches(actualType, currentSpec[j], args[i])) {
                                typeMatched = true;
                                break;
                            }
                        }
                        if (!typeMatched) {
                            throw new Error("TypeError: " + name + "() " + "expected argument " + (i + 1) + " to be type " + currentSpec + " but received type " + actualType + " instead.");
                        }
                    }
                },
                _typeMatches: function(actual, expected, argValue) {
                    if (expected === TYPE_ANY) {
                        return true;
                    }
                    if (expected === TYPE_ARRAY_STRING || expected === TYPE_ARRAY_NUMBER || expected === TYPE_ARRAY) {
                        if (expected === TYPE_ARRAY) {
                            return actual === TYPE_ARRAY;
                        } else if (actual === TYPE_ARRAY) {
                            var subtype;
                            if (expected === TYPE_ARRAY_NUMBER) {
                                subtype = TYPE_NUMBER;
                            } else if (expected === TYPE_ARRAY_STRING) {
                                subtype = TYPE_STRING;
                            }
                            for (var i = 0; i < argValue.length; i++) {
                                if (!this._typeMatches(this._getTypeName(argValue[i]), subtype, argValue[i])) {
                                    return false;
                                }
                            }
                            return true;
                        }
                    } else {
                        return actual === expected;
                    }
                },
                _getTypeName: function(obj) {
                    switch (Object.prototype.toString.call(obj)) {
                    case "[object String]":
                        return TYPE_STRING;
                    case "[object Number]":
                        return TYPE_NUMBER;
                    case "[object Array]":
                        return TYPE_ARRAY;
                    case "[object Boolean]":
                        return TYPE_BOOLEAN;
                    case "[object Null]":
                        return TYPE_NULL;
                    case "[object Object]":
                        if (obj.jmespathType === TOK_EXPREF) {
                            return TYPE_EXPREF;
                        } else {
                            return TYPE_OBJECT;
                        }
                    }
                },
                _functionStartsWith: function(resolvedArgs) {
                    return resolvedArgs[0].lastIndexOf(resolvedArgs[1]) === 0;
                },
                _functionEndsWith: function(resolvedArgs) {
                    var searchStr = resolvedArgs[0];
                    var suffix = resolvedArgs[1];
                    return searchStr.indexOf(suffix, searchStr.length - suffix.length) !== -1;
                },
                _functionReverse: function(resolvedArgs) {
                    var typeName = this._getTypeName(resolvedArgs[0]);
                    if (typeName === TYPE_STRING) {
                        var originalStr = resolvedArgs[0];
                        var reversedStr = "";
                        for (var i = originalStr.length - 1; i >= 0; i--) {
                            reversedStr += originalStr[i];
                        }
                        return reversedStr;
                    } else {
                        var reversedArray = resolvedArgs[0].slice(0);
                        reversedArray.reverse();
                        return reversedArray;
                    }
                },
                _functionAbs: function(resolvedArgs) {
                    return Math.abs(resolvedArgs[0]);
                },
                _functionCeil: function(resolvedArgs) {
                    return Math.ceil(resolvedArgs[0]);
                },
                _functionAvg: function(resolvedArgs) {
                    var sum = 0;
                    var inputArray = resolvedArgs[0];
                    for (var i = 0; i < inputArray.length; i++) {
                        sum += inputArray[i];
                    }
                    return sum / inputArray.length;
                },
                _functionContains: function(resolvedArgs) {
                    return resolvedArgs[0].indexOf(resolvedArgs[1]) >= 0;
                },
                _functionFloor: function(resolvedArgs) {
                    return Math.floor(resolvedArgs[0]);
                },
                _functionLength: function(resolvedArgs) {
                    if (!isObject(resolvedArgs[0])) {
                        return resolvedArgs[0].length;
                    } else {
                        return Object.keys(resolvedArgs[0]).length;
                    }
                },
                _functionMap: function(resolvedArgs) {
                    var mapped = [];
                    var interpreter = this._interpreter;
                    var exprefNode = resolvedArgs[0];
                    var elements = resolvedArgs[1];
                    for (var i = 0; i < elements.length; i++) {
                        mapped.push(interpreter.visit(exprefNode, elements[i]));
                    }
                    return mapped;
                },
                _functionMerge: function(resolvedArgs) {
                    var merged = {};
                    for (var i = 0; i < resolvedArgs.length; i++) {
                        var current = resolvedArgs[i];
                        for (var key in current) {
                            merged[key] = current[key];
                        }
                    }
                    return merged;
                },
                _functionMax: function(resolvedArgs) {
                    if (resolvedArgs[0].length > 0) {
                        var typeName = this._getTypeName(resolvedArgs[0][0]);
                        if (typeName === TYPE_NUMBER) {
                            return Math.max.apply(Math, resolvedArgs[0]);
                        } else {
                            var elements = resolvedArgs[0];
                            var maxElement = elements[0];
                            for (var i = 1; i < elements.length; i++) {
                                if (maxElement.localeCompare(elements[i]) < 0) {
                                    maxElement = elements[i];
                                }
                            }
                            return maxElement;
                        }
                    } else {
                        return null;
                    }
                },
                _functionMin: function(resolvedArgs) {
                    if (resolvedArgs[0].length > 0) {
                        var typeName = this._getTypeName(resolvedArgs[0][0]);
                        if (typeName === TYPE_NUMBER) {
                            return Math.min.apply(Math, resolvedArgs[0]);
                        } else {
                            var elements = resolvedArgs[0];
                            var minElement = elements[0];
                            for (var i = 1; i < elements.length; i++) {
                                if (elements[i].localeCompare(minElement) < 0) {
                                    minElement = elements[i];
                                }
                            }
                            return minElement;
                        }
                    } else {
                        return null;
                    }
                },
                _functionSum: function(resolvedArgs) {
                    var sum = 0;
                    var listToSum = resolvedArgs[0];
                    for (var i = 0; i < listToSum.length; i++) {
                        sum += listToSum[i];
                    }
                    return sum;
                },
                _functionType: function(resolvedArgs) {
                    switch (this._getTypeName(resolvedArgs[0])) {
                    case TYPE_NUMBER:
                        return "number";
                    case TYPE_STRING:
                        return "string";
                    case TYPE_ARRAY:
                        return "array";
                    case TYPE_OBJECT:
                        return "object";
                    case TYPE_BOOLEAN:
                        return "boolean";
                    case TYPE_EXPREF:
                        return "expref";
                    case TYPE_NULL:
                        return "null";
                    }
                },
                _functionKeys: function(resolvedArgs) {
                    return Object.keys(resolvedArgs[0]);
                },
                _functionValues: function(resolvedArgs) {
                    var obj = resolvedArgs[0];
                    var keys = Object.keys(obj);
                    var values = [];
                    for (var i = 0; i < keys.length; i++) {
                        values.push(obj[keys[i]]);
                    }
                    return values;
                },
                _functionJoin: function(resolvedArgs) {
                    var joinChar = resolvedArgs[0];
                    var listJoin = resolvedArgs[1];
                    return listJoin.join(joinChar);
                },
                _functionToArray: function(resolvedArgs) {
                    if (this._getTypeName(resolvedArgs[0]) === TYPE_ARRAY) {
                        return resolvedArgs[0];
                    } else {
                        return [resolvedArgs[0]];
                    }
                },
                _functionToString: function(resolvedArgs) {
                    if (this._getTypeName(resolvedArgs[0]) === TYPE_STRING) {
                        return resolvedArgs[0];
                    } else {
                        return JSON.stringify(resolvedArgs[0]);
                    }
                },
                _functionToNumber: function(resolvedArgs) {
                    var typeName = this._getTypeName(resolvedArgs[0]);
                    var convertedValue;
                    if (typeName === TYPE_NUMBER) {
                        return resolvedArgs[0];
                    } else if (typeName === TYPE_STRING) {
                        convertedValue = +resolvedArgs[0];
                        if (!isNaN(convertedValue)) {
                            return convertedValue;
                        }
                    }
                    return null;
                },
                _functionNotNull: function(resolvedArgs) {
                    for (var i = 0; i < resolvedArgs.length; i++) {
                        if (this._getTypeName(resolvedArgs[i]) !== TYPE_NULL) {
                            return resolvedArgs[i];
                        }
                    }
                    return null;
                },
                _functionSort: function(resolvedArgs) {
                    var sortedArray = resolvedArgs[0].slice(0);
                    sortedArray.sort();
                    return sortedArray;
                },
                _functionSortBy: function(resolvedArgs) {
                    var sortedArray = resolvedArgs[0].slice(0);
                    if (sortedArray.length === 0) {
                        return sortedArray;
                    }
                    var interpreter = this._interpreter;
                    var exprefNode = resolvedArgs[1];
                    var requiredType = this._getTypeName(interpreter.visit(exprefNode, sortedArray[0]));
                    if ([TYPE_NUMBER, TYPE_STRING].indexOf(requiredType) < 0) {
                        throw new Error("TypeError");
                    }
                    var that = this;
                    var decorated = [];
                    for (var i = 0; i < sortedArray.length; i++) {
                        decorated.push([i, sortedArray[i]]);
                    }
                    decorated.sort(function(a, b) {
                        var exprA = interpreter.visit(exprefNode, a[1]);
                        var exprB = interpreter.visit(exprefNode, b[1]);
                        if (that._getTypeName(exprA) !== requiredType) {
                            throw new Error("TypeError: expected " + requiredType + ", received " + that._getTypeName(exprA));
                        } else if (that._getTypeName(exprB) !== requiredType) {
                            throw new Error("TypeError: expected " + requiredType + ", received " + that._getTypeName(exprB));
                        }
                        if (exprA > exprB) {
                            return 1;
                        } else if (exprA < exprB) {
                            return -1;
                        } else {
                            return a[0] - b[0];
                        }
                    });
                    for (var j = 0; j < decorated.length; j++) {
                        sortedArray[j] = decorated[j][1];
                    }
                    return sortedArray;
                },
                _functionMaxBy: function(resolvedArgs) {
                    var exprefNode = resolvedArgs[1];
                    var resolvedArray = resolvedArgs[0];
                    var keyFunction = this.createKeyFunction(exprefNode, [TYPE_NUMBER, TYPE_STRING]);
                    var maxNumber = -Infinity;
                    var maxRecord;
                    var current;
                    for (var i = 0; i < resolvedArray.length; i++) {
                        current = keyFunction(resolvedArray[i]);
                        if (current > maxNumber) {
                            maxNumber = current;
                            maxRecord = resolvedArray[i];
                        }
                    }
                    return maxRecord;
                },
                _functionMinBy: function(resolvedArgs) {
                    var exprefNode = resolvedArgs[1];
                    var resolvedArray = resolvedArgs[0];
                    var keyFunction = this.createKeyFunction(exprefNode, [TYPE_NUMBER, TYPE_STRING]);
                    var minNumber = Infinity;
                    var minRecord;
                    var current;
                    for (var i = 0; i < resolvedArray.length; i++) {
                        current = keyFunction(resolvedArray[i]);
                        if (current < minNumber) {
                            minNumber = current;
                            minRecord = resolvedArray[i];
                        }
                    }
                    return minRecord;
                },
                createKeyFunction: function(exprefNode, allowedTypes) {
                    var that = this;
                    var interpreter = this._interpreter;
                    var keyFunc = function(x) {
                        var current = interpreter.visit(exprefNode, x);
                        if (allowedTypes.indexOf(that._getTypeName(current)) < 0) {
                            var msg = "TypeError: expected one of " + allowedTypes + ", received " + that._getTypeName(current);
                            throw new Error(msg);
                        }
                        return current;
                    };
                    return keyFunc;
                }
            };
            function compile(stream) {
                var parser = new Parser();
                var ast = parser.parse(stream);
                return ast;
            }
            function tokenize(stream) {
                var lexer = new Lexer();
                return lexer.tokenize(stream);
            }
            function search(data, expression) {
                var parser = new Parser();
                var runtime = new Runtime();
                var interpreter = new TreeInterpreter(runtime);
                runtime._interpreter = interpreter;
                var node = parser.parse(expression);
                return interpreter.search(node, data);
            }
            exports.tokenize = tokenize;
            exports.compile = compile;
            exports.search = search;
            exports.strictDeepEqual = strictDeepEqual;
        }
        )(false ? undefined : exports);
    }
    ), (function(module, exports, __webpack_require__) {
        exports.tryRequireThemeJsonEditor = function() {
            try {
                __webpack_require__(24);
            } catch (err) {
                console.error(err);
            }
        }
        ;
    }
    ), (function(module, exports, __webpack_require__) {
        "use strict";
        var ace = __webpack_require__(16);
        var VanillaPicker = __webpack_require__(13);
        var _require = __webpack_require__(26)
          , treeModeMixins = _require.treeModeMixins;
        var _require2 = __webpack_require__(17)
          , textModeMixins = _require2.textModeMixins;
        var _require3 = __webpack_require__(27)
          , previewModeMixins = _require3.previewModeMixins;
        var _require4 = __webpack_require__(0)
          , clear = _require4.clear
          , extend = _require4.extend
          , getInnerText = _require4.getInnerText
          , getInternetExplorerVersion = _require4.getInternetExplorerVersion
          , parse = _require4.parse;
        var _require5 = __webpack_require__(25)
          , tryRequireAjv = _require5.tryRequireAjv;
        var _require6 = __webpack_require__(6)
          , showTransformModal = _require6.showTransformModal;
        var _require7 = __webpack_require__(5)
          , showSortModal = _require7.showSortModal;
        var Ajv = tryRequireAjv();
        if (typeof Promise === 'undefined') {
            console.error('Promise undefined. Please load a Promise polyfill in the browser in order to use JSONEditor');
        }
        function JSONEditor(container, options, json) {
            if (!(this instanceof JSONEditor)) {
                throw new Error('JSONEditor constructor called without "new".');
            }
            var ieVersion = getInternetExplorerVersion();
            if (ieVersion !== -1 && ieVersion < 9) {
                throw new Error('Unsupported browser, IE9 or newer required. ' + 'Please install the newest version of your browser.');
            }
            if (options) {
                if (options.error) {
                    console.warn('Option "error" has been renamed to "onError"');
                    options.onError = options.error;
                    delete options.error;
                }
                if (options.change) {
                    console.warn('Option "change" has been renamed to "onChange"');
                    options.onChange = options.change;
                    delete options.change;
                }
                if (options.editable) {
                    console.warn('Option "editable" has been renamed to "onEditable"');
                    options.onEditable = options.editable;
                    delete options.editable;
                }
                if (options.onChangeJSON) {
                    if (options.mode === 'text' || options.mode === 'code' || options.modes && (options.modes.indexOf('text') !== -1 || options.modes.indexOf('code') !== -1)) {
                        console.warn('Option "onChangeJSON" is not applicable to modes "text" and "code". ' + 'Use "onChangeText" or "onChange" instead.');
                    }
                }
                if (options) {
                    Object.keys(options).forEach(function(option) {
                        if (JSONEditor.VALID_OPTIONS.indexOf(option) === -1) {
                            console.warn('Unknown option "' + option + '". This option will be ignored');
                        }
                    });
                }
            }
            if (arguments.length) {
                this._create(container, options, json);
            }
        }
        JSONEditor.modes = {};
        JSONEditor.prototype.DEBOUNCE_INTERVAL = 150;
        JSONEditor.VALID_OPTIONS = ['ajv', 'schema', 'schemaRefs', 'templates', 'ace', 'theme', 'autocomplete', 'onChange', 'onChangeJSON', 'onChangeText', 'onEditable', 'onError', 'onEvent', 'onModeChange', 'onNodeName', 'onValidate', 'onCreateMenu', 'onSelectionChange', 'onTextSelectionChange', 'onClassName', 'onFocus', 'onBlur', 'colorPicker', 'onColorPicker', 'timestampTag', 'timestampFormat', 'escapeUnicode', 'history', 'search', 'mode', 'modes', 'name', 'indentation', 'sortObjectKeys', 'navigationBar', 'statusBar', 'mainMenuBar', 'languages', 'language', 'enableSort', 'enableTransform', 'limitDragging', 'maxVisibleChilds', 'onValidationError', 'modalAnchor', 'popupAnchor', 'createQuery', 'executeQuery', 'queryDescription'];
        JSONEditor.prototype._create = function(container, options, json) {
            this.container = container;
            this.options = options || {};
            this.json = json || {};
            var mode = this.options.mode || this.options.modes && this.options.modes[0] || 'tree';
            this.setMode(mode);
        }
        ;
        JSONEditor.prototype.destroy = function() {}
        ;
        JSONEditor.prototype.set = function(json) {
            this.json = json;
        }
        ;
        JSONEditor.prototype.get = function() {
            return this.json;
        }
        ;
        JSONEditor.prototype.setText = function(jsonText) {
            this.json = parse(jsonText);
        }
        ;
        JSONEditor.prototype.getText = function() {
            return JSON.stringify(this.json);
        }
        ;
        JSONEditor.prototype.setName = function(name) {
            if (!this.options) {
                this.options = {};
            }
            this.options.name = name;
        }
        ;
        JSONEditor.prototype.getName = function() {
            return this.options && this.options.name;
        }
        ;
        JSONEditor.prototype.setMode = function(mode) {
            if (mode === this.options.mode && this.create) {
                return;
            }
            var container = this.container;
            var options = extend({}, this.options);
            var oldMode = options.mode;
            var data;
            var name;
            options.mode = mode;
            var config = JSONEditor.modes[mode];
            if (config) {
                try {
                    var asText = config.data === 'text';
                    name = this.getName();
                    data = this[asText ? 'getText' : 'get']();
                    this.destroy();
                    clear(this);
                    extend(this, config.mixin);
                    this.create(container, options);
                    this.setName(name);
                    this[asText ? 'setText' : 'set'](data);
                    if (typeof config.load === 'function') {
                        try {
                            config.load.call(this);
                        } catch (err) {
                            console.error(err);
                        }
                    }
                    if (typeof options.onModeChange === 'function' && mode !== oldMode) {
                        try {
                            options.onModeChange(mode, oldMode);
                        } catch (err) {
                            console.error(err);
                        }
                    }
                } catch (err) {
                    this._onError(err);
                }
            } else {
                throw new Error('Unknown mode "' + options.mode + '"');
            }
        }
        ;
        JSONEditor.prototype.getMode = function() {
            return this.options.mode;
        }
        ;
        JSONEditor.prototype._onError = function(err) {
            if (this.options && typeof this.options.onError === 'function') {
                this.options.onError(err);
            } else {
                throw err;
            }
        }
        ;
        JSONEditor.prototype.setSchema = function(schema, schemaRefs) {
            if (schema) {
                var ajv;
                try {
                    if (this.options.ajv) {
                        ajv = this.options.ajv;
                    } else {
                        ajv = Ajv({
                            allErrors: true,
                            verbose: true,
                            schemaId: 'auto',
                            $data: true
                        });
                        ajv.addMetaSchema(__webpack_require__(!(function webpackMissingModule() {
                            var e = new Error("Cannot find module 'ajv/lib/refs/json-schema-draft-04.json'");
                            e.code = 'MODULE_NOT_FOUND';
                            throw e;
                        }())));
                        ajv.addMetaSchema(__webpack_require__(!(function webpackMissingModule() {
                            var e = new Error("Cannot find module 'ajv/lib/refs/json-schema-draft-06.json'");
                            e.code = 'MODULE_NOT_FOUND';
                            throw e;
                        }())));
                    }
                } catch (err) {
                    console.warn('Failed to create an instance of Ajv, JSON Schema validation is not available. Please use a JSONEditor bundle including Ajv, or pass an instance of Ajv as via the configuration option `ajv`.');
                }
                if (ajv) {
                    if (schemaRefs) {
                        for (var ref in schemaRefs) {
                            ajv.removeSchema(ref);
                            if (schemaRefs[ref]) {
                                ajv.addSchema(schemaRefs[ref], ref);
                            }
                        }
                        this.options.schemaRefs = schemaRefs;
                    }
                    this.validateSchema = ajv.compile(schema);
                    this.options.schema = schema;
                    this.validate();
                }
                this.refresh();
            } else {
                this.validateSchema = null;
                this.options.schema = null;
                this.options.schemaRefs = null;
                this.validate();
                this.refresh();
            }
        }
        ;
        JSONEditor.prototype.validate = function() {}
        ;
        JSONEditor.prototype.refresh = function() {}
        ;
        JSONEditor.registerMode = function(mode) {
            var i, prop;
            if (Array.isArray(mode)) {
                for (i = 0; i < mode.length; i++) {
                    JSONEditor.registerMode(mode[i]);
                }
            } else {
                if (!('mode'in mode))
                    throw new Error('Property "mode" missing');
                if (!('mixin'in mode))
                    throw new Error('Property "mixin" missing');
                if (!('data'in mode))
                    throw new Error('Property "data" missing');
                var name = mode.mode;
                if (name in JSONEditor.modes) {
                    throw new Error('Mode "' + name + '" already registered');
                }
                if (typeof mode.mixin.create !== 'function') {
                    throw new Error('Required function "create" missing on mixin');
                }
                var reserved = ['setMode', 'registerMode', 'modes'];
                for (i = 0; i < reserved.length; i++) {
                    prop = reserved[i];
                    if (prop in mode.mixin) {
                        throw new Error('Reserved property "' + prop + '" not allowed in mixin');
                    }
                }
                JSONEditor.modes[name] = mode;
            }
        }
        ;
        JSONEditor.registerMode(treeModeMixins);
        JSONEditor.registerMode(textModeMixins);
        JSONEditor.registerMode(previewModeMixins);
        JSONEditor.ace = ace;
        JSONEditor.Ajv = Ajv;
        JSONEditor.VanillaPicker = VanillaPicker;
        JSONEditor.showTransformModal = showTransformModal;
        JSONEditor.showSortModal = showSortModal;
        JSONEditor.getInnerText = getInnerText;
        JSONEditor["default"] = JSONEditor;
        module.exports = JSONEditor;
    }
    ), (function(module, exports) {
        window.ace.define('ace/theme/jsoneditor', ['require', 'exports', 'module', 'ace/lib/dom'], function(acequire, exports, module) {
            exports.isDark = false;
            exports.cssClass = 'ace-jsoneditor';
            exports.cssText = ".ace-jsoneditor .ace_gutter {\nbackground: #ebebeb;\ncolor: #333\n}\n\n.ace-jsoneditor.ace_editor {\nfont-family: \"dejavu sans mono\", \"droid sans mono\", consolas, monaco, \"lucida console\", \"courier new\", courier, monospace, sans-serif;\nline-height: 1.3;\nbackground-color: #fff;\n}\n.ace-jsoneditor .ace_print-margin {\nwidth: 1px;\nbackground: #e8e8e8\n}\n.ace-jsoneditor .ace_scroller {\nbackground-color: #FFFFFF\n}\n.ace-jsoneditor .ace_text-layer {\ncolor: gray\n}\n.ace-jsoneditor .ace_variable {\ncolor: #1a1a1a\n}\n.ace-jsoneditor .ace_cursor {\nborder-left: 2px solid #000000\n}\n.ace-jsoneditor .ace_overwrite-cursors .ace_cursor {\nborder-left: 0px;\nborder-bottom: 1px solid #000000\n}\n.ace-jsoneditor .ace_marker-layer .ace_selection {\nbackground: lightgray\n}\n.ace-jsoneditor.ace_multiselect .ace_selection.ace_start {\nbox-shadow: 0 0 3px 0px #FFFFFF;\nborder-radius: 2px\n}\n.ace-jsoneditor .ace_marker-layer .ace_step {\nbackground: rgb(255, 255, 0)\n}\n.ace-jsoneditor .ace_marker-layer .ace_bracket {\nmargin: -1px 0 0 -1px;\nborder: 1px solid #BFBFBF\n}\n.ace-jsoneditor .ace_marker-layer .ace_active-line {\nbackground: #FFFBD1\n}\n.ace-jsoneditor .ace_gutter-active-line {\nbackground-color : #dcdcdc\n}\n.ace-jsoneditor .ace_marker-layer .ace_selected-word {\nborder: 1px solid lightgray\n}\n.ace-jsoneditor .ace_invisible {\ncolor: #BFBFBF\n}\n.ace-jsoneditor .ace_keyword,\n.ace-jsoneditor .ace_meta,\n.ace-jsoneditor .ace_support.ace_constant.ace_property-value {\ncolor: #AF956F\n}\n.ace-jsoneditor .ace_keyword.ace_operator {\ncolor: #484848\n}\n.ace-jsoneditor .ace_keyword.ace_other.ace_unit {\ncolor: #96DC5F\n}\n.ace-jsoneditor .ace_constant.ace_language {\ncolor: darkorange\n}\n.ace-jsoneditor .ace_constant.ace_numeric {\ncolor: red\n}\n.ace-jsoneditor .ace_constant.ace_character.ace_entity {\ncolor: #BF78CC\n}\n.ace-jsoneditor .ace_invalid {\ncolor: #FFFFFF;\nbackground-color: #FF002A;\n}\n.ace-jsoneditor .ace_fold {\nbackground-color: #AF956F;\nborder-color: #000000\n}\n.ace-jsoneditor .ace_storage,\n.ace-jsoneditor .ace_support.ace_class,\n.ace-jsoneditor .ace_support.ace_function,\n.ace-jsoneditor .ace_support.ace_other,\n.ace-jsoneditor .ace_support.ace_type {\ncolor: #C52727\n}\n.ace-jsoneditor .ace_string {\ncolor: green\n}\n.ace-jsoneditor .ace_comment {\ncolor: #BCC8BA\n}\n.ace-jsoneditor .ace_entity.ace_name.ace_tag,\n.ace-jsoneditor .ace_entity.ace_other.ace_attribute-name {\ncolor: #606060\n}\n.ace-jsoneditor .ace_markup.ace_underline {\ntext-decoration: underline\n}\n.ace-jsoneditor .ace_indent-guide {\nbackground: url(\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAAE0lEQVQImWP4////f4bLly//BwAmVgd1/w11/gAAAABJRU5ErkJggg==\") right repeat-y\n}";
            var dom = acequire('../lib/dom');
            dom.importCssString(exports.cssText, exports.cssClass);
        });
    }
    ), (function(module, exports, __webpack_require__) {
        exports.tryRequireAjv = function() {
            try {
                return __webpack_require__(!(function webpackMissingModule() {
                    var e = new Error("Cannot find module 'ajv'");
                    e.code = 'MODULE_NOT_FOUND';
                    throw e;
                }()));
            } catch (err) {}
        }
        ;
    }
    ), (function(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, "treeModeMixins", function() {
            return treeModeMixins;
        });
        var defaultFilterFunction = {
            start: function start(token, match, config) {
                return match.indexOf(token) === 0;
            },
            contain: function contain(token, match, config) {
                return match.indexOf(token) > -1;
            }
        };
        function autocomplete(config) {
            config = config || {};
            config.filter = config.filter || 'start';
            config.trigger = config.trigger || 'keydown';
            config.confirmKeys = config.confirmKeys || [39, 35, 9];
            config.caseSensitive = config.caseSensitive || false;
            var fontSize = '';
            var fontFamily = '';
            var wrapper = document.createElement('div');
            wrapper.style.position = 'relative';
            wrapper.style.outline = '0';
            wrapper.style.border = '0';
            wrapper.style.margin = '0';
            wrapper.style.padding = '0';
            var dropDown = document.createElement('div');
            dropDown.className = 'autocomplete dropdown';
            dropDown.style.position = 'absolute';
            dropDown.style.visibility = 'hidden';
            var spacer;
            var leftSide;
            var createDropDownController = function createDropDownController(elem, rs) {
                var rows = [];
                var ix = 0;
                var oldIndex = -1;
                var onMouseOver = function onMouseOver() {
                    this.style.backgroundColor = '#ddd';
                };
                var onMouseOut = function onMouseOut() {
                    this.style.backgroundColor = '';
                };
                var onMouseDown = function onMouseDown() {
                    p.hide();
                    p.onmouseselection(this.__hint, p.rs);
                };
                var p = {
                    rs: rs,
                    hide: function hide() {
                        elem.style.visibility = 'hidden';
                    },
                    refresh: function refresh(token, array) {
                        elem.style.visibility = 'hidden';
                        ix = 0;
                        elem.textContent = '';
                        var vph = window.innerHeight || document.documentElement.clientHeight;
                        var rect = elem.parentNode.getBoundingClientRect();
                        var distanceToTop = rect.top - 6;
                        var distanceToBottom = vph - rect.bottom - 6;
                        rows = [];
                        var filterFn = typeof config.filter === 'function' ? config.filter : defaultFilterFunction[config.filter];
                        var filtered = !filterFn ? [] : array.filter(function(match) {
                            return filterFn(config.caseSensitive ? token : token.toLowerCase(), config.caseSensitive ? match : match.toLowerCase(), config);
                        });
                        rows = filtered.map(function(row) {
                            var divRow = document.createElement('div');
                            divRow.className = 'item';
                            divRow.onmouseover = onMouseOver;
                            divRow.onmouseout = onMouseOut;
                            divRow.onmousedown = onMouseDown;
                            divRow.__hint = row;
                            divRow.textContent = '';
                            divRow.appendChild(document.createTextNode(row.substring(0, token.length)));
                            var b = document.createElement('b');
                            b.appendChild(document.createTextNode(row.substring(token.length)));
                            divRow.appendChild(b);
                            elem.appendChild(divRow);
                            return divRow;
                        });
                        if (rows.length === 0) {
                            return;
                        }
                        if (rows.length === 1 && (token.toLowerCase() === rows[0].__hint.toLowerCase() && !config.caseSensitive || token === rows[0].__hint && config.caseSensitive)) {
                            return;
                        }
                        if (rows.length < 2)
                            return;
                        p.highlight(0);
                        if (distanceToTop > distanceToBottom * 3) {
                            elem.style.maxHeight = distanceToTop + 'px';
                            elem.style.top = '';
                            elem.style.bottom = '100%';
                        } else {
                            elem.style.top = '100%';
                            elem.style.bottom = '';
                            elem.style.maxHeight = distanceToBottom + 'px';
                        }
                        elem.style.visibility = 'visible';
                    },
                    highlight: function highlight(index) {
                        if (oldIndex !== -1 && rows[oldIndex]) {
                            rows[oldIndex].className = 'item';
                        }
                        rows[index].className = 'item hover';
                        oldIndex = index;
                    },
                    move: function move(step) {
                        if (elem.style.visibility === 'hidden')
                            return '';
                        if (ix + step === -1 || ix + step === rows.length)
                            return rows[ix].__hint;
                        ix += step;
                        p.highlight(ix);
                        return rows[ix].__hint;
                    },
                    onmouseselection: function onmouseselection() {}
                };
                return p;
            };
            function setEndOfContenteditable(contentEditableElement) {
                var range, selection;
                if (document.createRange) {
                    range = document.createRange();
                    range.selectNodeContents(contentEditableElement);
                    range.collapse(false);
                    selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);
                } else if (document.selection) {
                    range = document.body.createTextRange();
                    range.moveToElementText(contentEditableElement);
                    range.collapse(false);
                    range.select();
                }
            }
            function calculateWidthForText(text) {
                if (spacer === undefined) {
                    spacer = document.createElement('span');
                    spacer.style.visibility = 'hidden';
                    spacer.style.position = 'fixed';
                    spacer.style.outline = '0';
                    spacer.style.margin = '0';
                    spacer.style.padding = '0';
                    spacer.style.border = '0';
                    spacer.style.left = '0';
                    spacer.style.whiteSpace = 'pre';
                    spacer.style.fontSize = fontSize;
                    spacer.style.fontFamily = fontFamily;
                    spacer.style.fontWeight = 'normal';
                    document.body.appendChild(spacer);
                }
                spacer.textContent = text;
                return spacer.getBoundingClientRect().right;
            }
            var rs = {
                onArrowDown: function onArrowDown() {},
                onArrowUp: function onArrowUp() {},
                onEnter: function onEnter() {},
                onTab: function onTab() {},
                startFrom: 0,
                options: [],
                element: null,
                elementHint: null,
                elementStyle: null,
                wrapper: wrapper,
                show: function show(element, startPos, options) {
                    var _this = this;
                    this.startFrom = startPos;
                    this.wrapper.remove();
                    if (this.elementHint) {
                        this.elementHint.remove();
                        this.elementHint = null;
                    }
                    if (fontSize === '') {
                        fontSize = window.getComputedStyle(element).getPropertyValue('font-size');
                    }
                    if (fontFamily === '') {
                        fontFamily = window.getComputedStyle(element).getPropertyValue('font-family');
                    }
                    dropDown.style.marginLeft = '0';
                    dropDown.style.marginTop = element.getBoundingClientRect().height + 'px';
                    this.options = options.map(String);
                    if (this.element !== element) {
                        this.element = element;
                        this.elementStyle = {
                            zIndex: this.element.style.zIndex,
                            position: this.element.style.position,
                            backgroundColor: this.element.style.backgroundColor,
                            borderColor: this.element.style.borderColor
                        };
                    }
                    this.element.style.zIndex = 3;
                    this.element.style.position = 'relative';
                    this.element.style.backgroundColor = 'transparent';
                    this.element.style.borderColor = 'transparent';
                    this.elementHint = element.cloneNode();
                    this.elementHint.className = 'autocomplete hint';
                    this.elementHint.style.zIndex = 2;
                    this.elementHint.style.position = 'absolute';
                    this.elementHint.onfocus = function() {
                        _this.element.focus();
                    }
                    ;
                    if (this.element.addEventListener) {
                        this.element.removeEventListener('keydown', keyDownHandler);
                        this.element.addEventListener('keydown', keyDownHandler, false);
                        this.element.removeEventListener('blur', onBlurHandler);
                        this.element.addEventListener('blur', onBlurHandler, false);
                    }
                    wrapper.appendChild(this.elementHint);
                    wrapper.appendChild(dropDown);
                    element.parentElement.appendChild(wrapper);
                    this.repaint(element);
                },
                setText: function setText(text) {
                    this.element.innerText = text;
                },
                getText: function getText() {
                    return this.element.innerText;
                },
                hideDropDown: function hideDropDown() {
                    this.wrapper.remove();
                    if (this.elementHint) {
                        this.elementHint.remove();
                        this.elementHint = null;
                        dropDownController.hide();
                        this.element.style.zIndex = this.elementStyle.zIndex;
                        this.element.style.position = this.elementStyle.position;
                        this.element.style.backgroundColor = this.elementStyle.backgroundColor;
                        this.element.style.borderColor = this.elementStyle.borderColor;
                    }
                },
                repaint: function repaint(element) {
                    var text = element.innerText;
                    text = text.replace('\n', '');
                    var optionsLength = this.options.length;
                    var token = text.substring(this.startFrom);
                    leftSide = text.substring(0, this.startFrom);
                    for (var i = 0; i < optionsLength; i++) {
                        var opt = this.options[i];
                        if (!config.caseSensitive && opt.toLowerCase().indexOf(token.toLowerCase()) === 0 || config.caseSensitive && opt.indexOf(token) === 0) {
                            this.elementHint.innerText = leftSide + token + opt.substring(token.length);
                            this.elementHint.realInnerText = leftSide + opt;
                            break;
                        }
                    }
                    dropDown.style.left = calculateWidthForText(leftSide) + 'px';
                    dropDownController.refresh(token, this.options);
                    this.elementHint.style.width = calculateWidthForText(this.elementHint.innerText) + 10 + 'px';
                    var wasDropDownHidden = dropDown.style.visibility === 'hidden';
                    if (!wasDropDownHidden) {
                        this.elementHint.style.width = calculateWidthForText(this.elementHint.innerText) + dropDown.clientWidth + 'px';
                    }
                }
            };
            var dropDownController = createDropDownController(dropDown, rs);
            var keyDownHandler = function(e) {
                e = e || window.event;
                var keyCode = e.keyCode;
                if (this.elementHint == null)
                    return;
                if (keyCode === 33) {
                    return;
                }
                if (keyCode === 34) {
                    return;
                }
                if (keyCode === 27) {
                    rs.hideDropDown();
                    rs.element.focus();
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
                var text = this.element.innerText;
                text = text.replace('\n', '');
                if (config.confirmKeys.indexOf(keyCode) >= 0) {
                    if (keyCode === 9) {
                        if (this.elementHint.innerText.length === 0) {
                            rs.onTab();
                        }
                    }
                    if (this.elementHint.innerText.length > 0) {
                        if (this.element.innerText !== this.elementHint.realInnerText) {
                            this.element.innerText = this.elementHint.realInnerText;
                            rs.hideDropDown();
                            setEndOfContenteditable(this.element);
                            if (keyCode === 9) {
                                rs.element.focus();
                                e.preventDefault();
                                e.stopPropagation();
                            }
                        }
                    }
                    return;
                }
                if (keyCode === 13) {
                    if (this.elementHint.innerText.length === 0) {
                        rs.onEnter();
                    } else {
                        var wasDropDownHidden = dropDown.style.visibility === 'hidden';
                        dropDownController.hide();
                        if (wasDropDownHidden) {
                            rs.hideDropDown();
                            rs.element.focus();
                            rs.onEnter();
                            return;
                        }
                        this.element.innerText = this.elementHint.realInnerText;
                        rs.hideDropDown();
                        setEndOfContenteditable(this.element);
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    return;
                }
                if (keyCode === 40) {
                    var token = text.substring(this.startFrom);
                    var m = dropDownController.move(+1);
                    if (m === '') {
                        rs.onArrowDown();
                    }
                    this.elementHint.innerText = leftSide + token + m.substring(token.length);
                    this.elementHint.realInnerText = leftSide + m;
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
                if (keyCode === 38) {
                    var _token = text.substring(this.startFrom);
                    var _m = dropDownController.move(-1);
                    if (_m === '') {
                        rs.onArrowUp();
                    }
                    this.elementHint.innerText = leftSide + _token + _m.substring(_token.length);
                    this.elementHint.realInnerText = leftSide + _m;
                    e.preventDefault();
                    e.stopPropagation();
                }
            }
            .bind(rs);
            var onBlurHandler = function onBlurHandler(e) {
                rs.hideDropDown();
            };
            dropDownController.onmouseselection = function(text, rs) {
                rs.element.innerText = rs.elementHint.innerText = leftSide + text;
                rs.hideDropDown();
                window.setTimeout(function() {
                    rs.element.focus();
                    setEndOfContenteditable(rs.element);
                }, 1);
            }
            ;
            return rs;
        }
        var ContextMenu = __webpack_require__(3);
        var FocusTracker = __webpack_require__(8);
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value"in descriptor)
                    descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps)
                _defineProperties(Constructor.prototype, protoProps);
            if (staticProps)
                _defineProperties(Constructor, staticProps);
            return Constructor;
        }
        var Highlighter = function() {
            function Highlighter() {
                _classCallCheck(this, Highlighter);
                this.locked = false;
            }
            _createClass(Highlighter, [{
                key: "highlight",
                value: function highlight(node) {
                    if (this.locked) {
                        return;
                    }
                    if (this.node !== node) {
                        if (this.node) {
                            this.node.setHighlight(false);
                        }
                        this.node = node;
                        this.node.setHighlight(true);
                    }
                    this._cancelUnhighlight();
                }
            }, {
                key: "unhighlight",
                value: function unhighlight() {
                    if (this.locked) {
                        return;
                    }
                    var me = this;
                    if (this.node) {
                        this._cancelUnhighlight();
                        this.unhighlightTimer = setTimeout(function() {
                            me.node.setHighlight(false);
                            me.node = undefined;
                            me.unhighlightTimer = undefined;
                        }, 0);
                    }
                }
            }, {
                key: "_cancelUnhighlight",
                value: function _cancelUnhighlight() {
                    if (this.unhighlightTimer) {
                        clearTimeout(this.unhighlightTimer);
                        this.unhighlightTimer = undefined;
                    }
                }
            }, {
                key: "lock",
                value: function lock() {
                    this.locked = true;
                }
            }, {
                key: "unlock",
                value: function unlock() {
                    this.locked = false;
                }
            }]);
            return Highlighter;
        }();
        var i18n = __webpack_require__(1);
        var jmespathQuery = __webpack_require__(4);
        var ModeSwitcher = __webpack_require__(9);
        var naturalSort = __webpack_require__(12);
        var naturalSort_default = __webpack_require__.n(naturalSort);
        var createAbsoluteAnchor = __webpack_require__(11);
        var util = __webpack_require__(0);
        function appendNodeFactory(Node) {
            function AppendNode(editor) {
                this.editor = editor;
                this.dom = {};
            }
            AppendNode.prototype = new Node();
            AppendNode.prototype.getDom = function() {
                var dom = this.dom;
                if (dom.tr) {
                    return dom.tr;
                }
                this._updateEditability();
                var trAppend = document.createElement('tr');
                trAppend.className = 'jsoneditor-append';
                trAppend.node = this;
                dom.tr = trAppend;
                if (this.editor.options.mode === 'tree') {
                    dom.tdDrag = document.createElement('td');
                    var tdMenu = document.createElement('td');
                    dom.tdMenu = tdMenu;
                    var menu = document.createElement('button');
                    menu.type = 'button';
                    menu.className = 'jsoneditor-button jsoneditor-contextmenu-button';
                    menu.title = 'Click to open the actions menu (Ctrl+M)';
                    dom.menu = menu;
                    tdMenu.appendChild(dom.menu);
                }
                var tdAppend = document.createElement('td');
                var domText = document.createElement('div');
                domText.appendChild(document.createTextNode('(' + Object(i18n["c"])('empty') + ')'));
                domText.className = 'jsoneditor-readonly';
                tdAppend.appendChild(domText);
                dom.td = tdAppend;
                dom.text = domText;
                this.updateDom();
                return trAppend;
            }
            ;
            AppendNode.prototype.getPath = function() {
                return null;
            }
            ;
            AppendNode.prototype.getIndex = function() {
                return null;
            }
            ;
            AppendNode.prototype.updateDom = function(options) {
                var dom = this.dom;
                var tdAppend = dom.td;
                if (tdAppend) {
                    tdAppend.style.paddingLeft = this.getLevel() * 24 + 26 + 'px';
                }
                var domText = dom.text;
                if (domText) {
                    domText.firstChild.nodeValue = '(' + Object(i18n["c"])('empty') + ' ' + this.parent.type + ')';
                }
                var trAppend = dom.tr;
                if (!this.isVisible()) {
                    if (dom.tr.firstChild) {
                        if (dom.tdDrag) {
                            trAppend.removeChild(dom.tdDrag);
                        }
                        if (dom.tdMenu) {
                            trAppend.removeChild(dom.tdMenu);
                        }
                        trAppend.removeChild(tdAppend);
                    }
                } else {
                    if (!dom.tr.firstChild) {
                        if (dom.tdDrag) {
                            trAppend.appendChild(dom.tdDrag);
                        }
                        if (dom.tdMenu) {
                            trAppend.appendChild(dom.tdMenu);
                        }
                        trAppend.appendChild(tdAppend);
                    }
                }
            }
            ;
            AppendNode.prototype.isVisible = function() {
                return this.parent.childs.length === 0;
            }
            ;
            AppendNode.prototype.showContextMenu = function(anchor, onClose) {
                var node = this;
                var appendSubmenu = [{
                    text: Object(i18n["c"])('auto'),
                    className: 'jsoneditor-type-auto',
                    title: Object(i18n["c"])('autoType'),
                    click: function click() {
                        node._onAppend('', '', 'auto');
                    }
                }, {
                    text: Object(i18n["c"])('array'),
                    className: 'jsoneditor-type-array',
                    title: Object(i18n["c"])('arrayType'),
                    click: function click() {
                        node._onAppend('', []);
                    }
                }, {
                    text: Object(i18n["c"])('object'),
                    className: 'jsoneditor-type-object',
                    title: Object(i18n["c"])('objectType'),
                    click: function click() {
                        node._onAppend('', {});
                    }
                }, {
                    text: Object(i18n["c"])('string'),
                    className: 'jsoneditor-type-string',
                    title: Object(i18n["c"])('stringType'),
                    click: function click() {
                        node._onAppend('', '', 'string');
                    }
                }];
                node.addTemplates(appendSubmenu, true);
                var items = [{
                    text: Object(i18n["c"])('appendText'),
                    title: Object(i18n["c"])('appendTitleAuto'),
                    submenuTitle: Object(i18n["c"])('appendSubmenuTitle'),
                    className: 'jsoneditor-insert',
                    click: function click() {
                        node._onAppend('', '', 'auto');
                    },
                    submenu: appendSubmenu
                }];
                if (this.editor.options.onCreateMenu) {
                    var path = node.parent.getPath();
                    items = this.editor.options.onCreateMenu(items, {
                        type: 'append',
                        path: path,
                        paths: [path]
                    });
                }
                var menu = new ContextMenu["a"](items,{
                    close: onClose
                });
                menu.show(anchor, this.editor.getPopupAnchor());
            }
            ;
            AppendNode.prototype.onEvent = function(event) {
                var type = event.type;
                var target = event.target || event.srcElement;
                var dom = this.dom;
                var menu = dom.menu;
                if (target === menu) {
                    if (type === 'mouseover') {
                        this.editor.highlighter.highlight(this.parent);
                    } else if (type === 'mouseout') {
                        this.editor.highlighter.unhighlight();
                    }
                }
                if (type === 'click' && target === dom.menu) {
                    var highlighter = this.editor.highlighter;
                    highlighter.highlight(this.parent);
                    highlighter.lock();
                    Object(util["addClassName"])(dom.menu, 'jsoneditor-selected');
                    this.showContextMenu(dom.menu, function() {
                        Object(util["removeClassName"])(dom.menu, 'jsoneditor-selected');
                        highlighter.unlock();
                        highlighter.unhighlight();
                    });
                }
                if (type === 'keydown') {
                    this.onKeyDown(event);
                }
            }
            ;
            return AppendNode;
        }
        function showMoreNodeFactory(Node) {
            function ShowMoreNode(editor, parent) {
                this.editor = editor;
                this.parent = parent;
                this.dom = {};
            }
            ShowMoreNode.prototype = new Node();
            ShowMoreNode.prototype.getDom = function() {
                if (this.dom.tr) {
                    return this.dom.tr;
                }
                this._updateEditability();
                if (!this.dom.tr) {
                    var me = this;
                    var parent = this.parent;
                    var showMoreButton = document.createElement('a');
                    showMoreButton.appendChild(document.createTextNode(Object(i18n["c"])('showMore')));
                    showMoreButton.href = '#';
                    showMoreButton.onclick = function(event) {
                        parent.visibleChilds = Math.floor(parent.visibleChilds / parent.getMaxVisibleChilds() + 1) * parent.getMaxVisibleChilds();
                        me.updateDom();
                        parent.showChilds();
                        event.preventDefault();
                        return false;
                    }
                    ;
                    var showAllButton = document.createElement('a');
                    showAllButton.appendChild(document.createTextNode(Object(i18n["c"])('showAll')));
                    showAllButton.href = '#';
                    showAllButton.onclick = function(event) {
                        parent.visibleChilds = Infinity;
                        me.updateDom();
                        parent.showChilds();
                        event.preventDefault();
                        return false;
                    }
                    ;
                    var moreContents = document.createElement('div');
                    var moreText = document.createTextNode(this._getShowMoreText());
                    moreContents.className = 'jsoneditor-show-more';
                    moreContents.appendChild(moreText);
                    moreContents.appendChild(showMoreButton);
                    moreContents.appendChild(document.createTextNode('. '));
                    moreContents.appendChild(showAllButton);
                    moreContents.appendChild(document.createTextNode('. '));
                    var tdContents = document.createElement('td');
                    tdContents.appendChild(moreContents);
                    var moreTr = document.createElement('tr');
                    if (this.editor.options.mode === 'tree') {
                        moreTr.appendChild(document.createElement('td'));
                        moreTr.appendChild(document.createElement('td'));
                    }
                    moreTr.appendChild(tdContents);
                    moreTr.className = 'jsoneditor-show-more';
                    this.dom.tr = moreTr;
                    this.dom.moreContents = moreContents;
                    this.dom.moreText = moreText;
                }
                this.updateDom();
                return this.dom.tr;
            }
            ;
            ShowMoreNode.prototype.updateDom = function(options) {
                if (this.isVisible()) {
                    this.dom.tr.node = this.parent.childs[this.parent.visibleChilds];
                    if (!this.dom.tr.parentNode) {
                        var nextTr = this.parent._getNextTr();
                        if (nextTr) {
                            nextTr.parentNode.insertBefore(this.dom.tr, nextTr);
                        }
                    }
                    this.dom.moreText.nodeValue = this._getShowMoreText();
                    this.dom.moreContents.style.marginLeft = (this.getLevel() + 1) * 24 + 'px';
                } else {
                    if (this.dom.tr && this.dom.tr.parentNode) {
                        this.dom.tr.parentNode.removeChild(this.dom.tr);
                    }
                }
            }
            ;
            ShowMoreNode.prototype._getShowMoreText = function() {
                return Object(i18n["c"])('showMoreStatus', {
                    visibleChilds: this.parent.visibleChilds,
                    totalChilds: this.parent.childs.length
                }) + ' ';
            }
            ;
            ShowMoreNode.prototype.isVisible = function() {
                return this.parent.expanded && this.parent.childs.length > this.parent.visibleChilds;
            }
            ;
            ShowMoreNode.prototype.onEvent = function(event) {
                var type = event.type;
                if (type === 'keydown') {
                    this.onKeyDown(event);
                }
            }
            ;
            return ShowMoreNode;
        }
        var js_showSortModal = __webpack_require__(5);
        var js_showTransformModal = __webpack_require__(6);
        var constants = __webpack_require__(2);
        function _createForOfIteratorHelper(o, allowArrayLike) {
            var it;
            if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) {
                if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
                    if (it)
                        o = it;
                    var i = 0;
                    var F = function F() {};
                    return {
                        s: F,
                        n: function n() {
                            if (i >= o.length)
                                return {
                                    done: true
                                };
                            return {
                                done: false,
                                value: o[i++]
                            };
                        },
                        e: function e(_e) {
                            throw _e;
                        },
                        f: F
                    };
                }
                throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
            }
            var normalCompletion = true, didErr = false, err;
            return {
                s: function s() {
                    it = o[Symbol.iterator]();
                },
                n: function n() {
                    var step = it.next();
                    normalCompletion = step.done;
                    return step;
                },
                e: function e(_e2) {
                    didErr = true;
                    err = _e2;
                },
                f: function f() {
                    try {
                        if (!normalCompletion && it["return"] != null)
                            it["return"]();
                    } finally {
                        if (didErr)
                            throw err;
                    }
                }
            };
        }
        function _unsupportedIterableToArray(o, minLen) {
            if (!o)
                return;
            if (typeof o === "string")
                return _arrayLikeToArray(o, minLen);
            var n = Object.prototype.toString.call(o).slice(8, -1);
            if (n === "Object" && o.constructor)
                n = o.constructor.name;
            if (n === "Map" || n === "Set")
                return Array.from(o);
            if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))
                return _arrayLikeToArray(o, minLen);
        }
        function _arrayLikeToArray(arr, len) {
            if (len == null || len > arr.length)
                len = arr.length;
            for (var i = 0, arr2 = new Array(len); i < len; i++) {
                arr2[i] = arr[i];
            }
            return arr2;
        }
        function _typeof(obj) {
            "@babel/helpers - typeof";
            if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
                _typeof = function _typeof(obj) {
                    return typeof obj;
                }
                ;
            } else {
                _typeof = function _typeof(obj) {
                    return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
                }
                ;
            }
            return _typeof(obj);
        }
        function Node_classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function Node_defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value"in descriptor)
                    descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function Node_createClass(Constructor, protoProps, staticProps) {
            if (protoProps)
                Node_defineProperties(Constructor.prototype, protoProps);
            if (staticProps)
                Node_defineProperties(Constructor, staticProps);
            return Constructor;
        }
        var Node_Node = function() {
            function Node(editor, params) {
                Node_classCallCheck(this, Node);
                this.editor = editor;
                this.dom = {};
                this.expanded = false;
                if (params && params instanceof Object) {
                    this.setField(params.field, params.fieldEditable);
                    if ('value'in params) {
                        this.setValue(params.value, params.type);
                    }
                    if ('internalValue'in params) {
                        this.setInternalValue(params.internalValue);
                    }
                } else {
                    this.setField('');
                    this.setValue(null);
                }
                this._debouncedOnChangeValue = Object(util["debounce"])(this._onChangeValue.bind(this), Node.prototype.DEBOUNCE_INTERVAL);
                this._debouncedOnChangeField = Object(util["debounce"])(this._onChangeField.bind(this), Node.prototype.DEBOUNCE_INTERVAL);
                this.visibleChilds = this.getMaxVisibleChilds();
            }
            Node_createClass(Node, [{
                key: "getMaxVisibleChilds",
                value: function getMaxVisibleChilds() {
                    return this.editor && this.editor.options && this.editor.options.maxVisibleChilds ? this.editor.options.maxVisibleChilds : DEFAULT_MAX_VISIBLE_CHILDS;
                }
            }, {
                key: "_updateEditability",
                value: function _updateEditability() {
                    this.editable = {
                        field: true,
                        value: true
                    };
                    if (this.editor) {
                        this.editable.field = this.editor.options.mode === 'tree';
                        this.editable.value = this.editor.options.mode !== 'view';
                        if ((this.editor.options.mode === 'tree' || this.editor.options.mode === 'form') && typeof this.editor.options.onEditable === 'function') {
                            var editable = this.editor.options.onEditable({
                                field: this.field,
                                value: this.value,
                                path: this.getPath()
                            });
                            if (typeof editable === 'boolean') {
                                this.editable.field = editable;
                                this.editable.value = editable;
                            } else if (_typeof(editable) === 'object' && editable !== null) {
                                if (typeof editable.field === 'boolean')
                                    this.editable.field = editable.field;
                                if (typeof editable.value === 'boolean')
                                    this.editable.value = editable.value;
                            } else {
                                console.error('Invalid return value for function onEditable.', 'Actual value:', editable, '.', 'Either a boolean or object { field: boolean, value: boolean } expected.');
                                this.editable.field = false;
                                this.editable.value = false;
                            }
                        }
                    }
                }
            }, {
                key: "getPath",
                value: function getPath() {
                    var node = this;
                    var path = [];
                    while (node) {
                        var field = node.getName();
                        if (field !== undefined) {
                            path.unshift(field);
                        }
                        node = node.parent;
                    }
                    return path;
                }
            }, {
                key: "getInternalPath",
                value: function getInternalPath() {
                    var node = this;
                    var internalPath = [];
                    while (node) {
                        if (node.parent) {
                            internalPath.unshift(node.getIndex());
                        }
                        node = node.parent;
                    }
                    return internalPath;
                }
            }, {
                key: "getName",
                value: function getName() {
                    return !this.parent ? undefined : this.parent.type !== 'array' ? this.field : this.index;
                }
            }, {
                key: "findNodeByPath",
                value: function findNodeByPath(path) {
                    if (!path) {
                        return;
                    }
                    if (path.length === 0) {
                        return this;
                    }
                    if (path.length && this.childs && this.childs.length) {
                        for (var i = 0; i < this.childs.length; ++i) {
                            if ('' + path[0] === '' + this.childs[i].getName()) {
                                return this.childs[i].findNodeByPath(path.slice(1));
                            }
                        }
                    }
                }
            }, {
                key: "findNodeByInternalPath",
                value: function findNodeByInternalPath(internalPath) {
                    if (!internalPath) {
                        return undefined;
                    }
                    var node = this;
                    for (var i = 0; i < internalPath.length && node; i++) {
                        var childIndex = internalPath[i];
                        node = node.childs[childIndex];
                    }
                    return node;
                }
            }, {
                key: "serialize",
                value: function serialize() {
                    return {
                        value: this.getValue(),
                        path: this.getPath()
                    };
                }
            }, {
                key: "findNode",
                value: function findNode(jsonPath) {
                    var path = Object(util["parsePath"])(jsonPath);
                    var node = this;
                    var _loop = function _loop() {
                        var prop = path.shift();
                        if (typeof prop === 'number') {
                            if (node.type !== 'array') {
                                throw new Error('Cannot get child node at index ' + prop + ': node is no array');
                            }
                            node = node.childs[prop];
                        } else {
                            if (node.type !== 'object') {
                                throw new Error('Cannot get child node ' + prop + ': node is no object');
                            }
                            node = node.childs.filter(function(child) {
                                return child.field === prop;
                            })[0];
                        }
                    };
                    while (node && path.length > 0) {
                        _loop();
                    }
                    return node;
                }
            }, {
                key: "findParents",
                value: function findParents() {
                    var parents = [];
                    var parent = this.parent;
                    while (parent) {
                        parents.unshift(parent);
                        parent = parent.parent;
                    }
                    return parents;
                }
            }, {
                key: "setError",
                value: function setError(error, child) {
                    this.error = error;
                    this.errorChild = child;
                    if (this.dom && this.dom.tr) {
                        this.updateError();
                    }
                }
            }, {
                key: "updateError",
                value: function updateError() {
                    var _this = this;
                    var error = this.fieldError || this.valueError || this.error;
                    var tdError = this.dom.tdError;
                    if (error && this.dom && this.dom.tr) {
                        Object(util["addClassName"])(this.dom.tr, 'jsoneditor-validation-error');
                        if (!tdError) {
                            tdError = document.createElement('td');
                            this.dom.tdError = tdError;
                            this.dom.tdValue.parentNode.appendChild(tdError);
                        }
                        var button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'jsoneditor-button jsoneditor-schema-error';
                        var destroy = function destroy() {
                            if (_this.dom.popupAnchor) {
                                _this.dom.popupAnchor.destroy();
                            }
                        };
                        var onDestroy = function onDestroy() {
                            delete _this.dom.popupAnchor;
                        };
                        var createPopup = function createPopup(destroyOnMouseOut) {
                            var frame = _this.editor.frame;
                            _this.dom.popupAnchor = Object(createAbsoluteAnchor["a"])(button, _this.editor.getPopupAnchor(), onDestroy, destroyOnMouseOut);
                            var popupWidth = 200;
                            var buttonRect = button.getBoundingClientRect();
                            var frameRect = frame.getBoundingClientRect();
                            var position = frameRect.width - buttonRect.x > popupWidth / 2 + 20 ? 'jsoneditor-above' : 'jsoneditor-left';
                            var popover = document.createElement('div');
                            popover.className = 'jsoneditor-popover ' + position;
                            popover.appendChild(document.createTextNode(error.message));
                            _this.dom.popupAnchor.appendChild(popover);
                        };
                        button.onmouseover = function() {
                            if (!_this.dom.popupAnchor) {
                                createPopup(true);
                            }
                        }
                        ;
                        button.onfocus = function() {
                            destroy();
                            createPopup(false);
                        }
                        ;
                        button.onblur = function() {
                            destroy();
                        }
                        ;
                        var child = this.errorChild;
                        if (child) {
                            button.onclick = function showInvalidNode() {
                                child.findParents().forEach(function(parent) {
                                    parent.expand(false);
                                });
                                child.scrollTo(function() {
                                    child.focus();
                                });
                            }
                            ;
                        }
                        while (tdError.firstChild) {
                            tdError.removeChild(tdError.firstChild);
                        }
                        tdError.appendChild(button);
                    } else {
                        if (this.dom.tr) {
                            Object(util["removeClassName"])(this.dom.tr, 'jsoneditor-validation-error');
                        }
                        if (tdError) {
                            this.dom.tdError.parentNode.removeChild(this.dom.tdError);
                            delete this.dom.tdError;
                        }
                    }
                }
            }, {
                key: "getIndex",
                value: function getIndex() {
                    if (this.parent) {
                        var index = this.parent.childs.indexOf(this);
                        return index !== -1 ? index : null;
                    } else {
                        return -1;
                    }
                }
            }, {
                key: "setParent",
                value: function setParent(parent) {
                    this.parent = parent;
                }
            }, {
                key: "setField",
                value: function setField(field, fieldEditable) {
                    this.field = field;
                    this.previousField = field;
                    this.fieldEditable = fieldEditable === true;
                }
            }, {
                key: "getField",
                value: function getField() {
                    if (this.field === undefined) {
                        this._getDomField();
                    }
                    return this.field;
                }
            }, {
                key: "setValue",
                value: function setValue(value, type) {
                    var childValue, child;
                    var i, j;
                    var updateDom = false;
                    var previousChilds = this.childs;
                    this.type = this._getType(value);
                    if (type && type !== this.type) {
                        if (type === 'string' && this.type === 'auto') {
                            this.type = type;
                        } else {
                            throw new Error('Type mismatch: ' + 'cannot cast value of type "' + this.type + ' to the specified type "' + type + '"');
                        }
                    }
                    if (this.type === 'array') {
                        if (!this.childs) {
                            this.childs = [];
                        }
                        for (i = 0; i < value.length; i++) {
                            childValue = value[i];
                            if (childValue !== undefined && !(childValue instanceof Function)) {
                                if (i < this.childs.length) {
                                    child = this.childs[i];
                                    child.fieldEditable = false;
                                    child.index = i;
                                    child.setValue(childValue);
                                } else {
                                    child = new Node(this.editor,{
                                        value: childValue
                                    });
                                    var visible = i < this.getMaxVisibleChilds();
                                    this.appendChild(child, visible, updateDom);
                                }
                            }
                        }
                        for (j = this.childs.length; j >= value.length; j--) {
                            this.removeChild(this.childs[j], updateDom);
                        }
                    } else if (this.type === 'object') {
                        if (!this.childs) {
                            this.childs = [];
                        }
                        for (j = this.childs.length - 1; j >= 0; j--) {
                            if (!Node_hasOwnProperty(value, this.childs[j].field)) {
                                this.removeChild(this.childs[j], updateDom);
                            }
                        }
                        i = 0;
                        for (var childField in value) {
                            if (Node_hasOwnProperty(value, childField)) {
                                childValue = value[childField];
                                if (childValue !== undefined && !(childValue instanceof Function)) {
                                    var _child = this.findChildByProperty(childField);
                                    if (_child) {
                                        _child.setField(childField, true);
                                        _child.setValue(childValue);
                                    } else {
                                        var newChild = new Node(this.editor,{
                                            field: childField,
                                            value: childValue
                                        });
                                        var _visible = i < this.getMaxVisibleChilds();
                                        this.appendChild(newChild, _visible, updateDom);
                                    }
                                }
                                i++;
                            }
                        }
                        this.value = '';
                        if (this.editor.options.sortObjectKeys === true) {
                            var triggerAction = false;
                            this.sort([], 'asc', triggerAction);
                        }
                    } else {
                        this.hideChilds();
                        delete this.append;
                        delete this.showMore;
                        delete this.expanded;
                        delete this.childs;
                        this.value = value;
                    }
                    if (Array.isArray(previousChilds) !== Array.isArray(this.childs)) {
                        this.recreateDom();
                    }
                    this.updateDom({
                        updateIndexes: true
                    });
                    this.previousValue = this.value;
                }
            }, {
                key: "setInternalValue",
                value: function setInternalValue(internalValue) {
                    var childValue, child, visible;
                    var i, j;
                    var notUpdateDom = false;
                    var previousChilds = this.childs;
                    this.type = internalValue.type;
                    if (internalValue.type === 'array') {
                        if (!this.childs) {
                            this.childs = [];
                        }
                        for (i = 0; i < internalValue.childs.length; i++) {
                            childValue = internalValue.childs[i];
                            if (childValue !== undefined && !(childValue instanceof Function)) {
                                if (i < this.childs.length) {
                                    child = this.childs[i];
                                    child.fieldEditable = false;
                                    child.index = i;
                                    child.setInternalValue(childValue);
                                } else {
                                    child = new Node(this.editor,{
                                        internalValue: childValue
                                    });
                                    visible = i < this.getMaxVisibleChilds();
                                    this.appendChild(child, visible, notUpdateDom);
                                }
                            }
                        }
                        for (j = this.childs.length; j >= internalValue.childs.length; j--) {
                            this.removeChild(this.childs[j], notUpdateDom);
                        }
                    } else if (internalValue.type === 'object') {
                        if (!this.childs) {
                            this.childs = [];
                        }
                        for (i = 0; i < internalValue.childs.length; i++) {
                            childValue = internalValue.childs[i];
                            if (childValue !== undefined && !(childValue instanceof Function)) {
                                if (i < this.childs.length) {
                                    child = this.childs[i];
                                    delete child.index;
                                    child.setField(childValue.field, true);
                                    child.setInternalValue(childValue.value);
                                } else {
                                    child = new Node(this.editor,{
                                        field: childValue.field,
                                        internalValue: childValue.value
                                    });
                                    visible = i < this.getMaxVisibleChilds();
                                    this.appendChild(child, visible, notUpdateDom);
                                }
                            }
                        }
                        for (j = this.childs.length; j >= internalValue.childs.length; j--) {
                            this.removeChild(this.childs[j], notUpdateDom);
                        }
                    } else {
                        this.hideChilds();
                        delete this.append;
                        delete this.showMore;
                        delete this.expanded;
                        delete this.childs;
                        this.value = internalValue.value;
                    }
                    if (Array.isArray(previousChilds) !== Array.isArray(this.childs)) {
                        this.recreateDom();
                    }
                    this.updateDom({
                        updateIndexes: true
                    });
                    this.previousValue = this.value;
                }
            }, {
                key: "recreateDom",
                value: function recreateDom() {
                    if (this.dom && this.dom.tr && this.dom.tr.parentNode) {
                        var domAnchor = this._detachFromDom();
                        this.clearDom();
                        this._attachToDom(domAnchor);
                    } else {
                        this.clearDom();
                    }
                }
            }, {
                key: "getValue",
                value: function getValue() {
                    if (this.type === 'array') {
                        var arr = [];
                        this.childs.forEach(function(child) {
                            arr.push(child.getValue());
                        });
                        return arr;
                    } else if (this.type === 'object') {
                        var obj = {};
                        this.childs.forEach(function(child) {
                            obj[child.getField()] = child.getValue();
                        });
                        return obj;
                    } else {
                        if (this.value === undefined) {
                            this._getDomValue();
                        }
                        return this.value;
                    }
                }
            }, {
                key: "getInternalValue",
                value: function getInternalValue() {
                    if (this.type === 'array') {
                        return {
                            type: this.type,
                            childs: this.childs.map(function(child) {
                                return child.getInternalValue();
                            })
                        };
                    } else if (this.type === 'object') {
                        return {
                            type: this.type,
                            childs: this.childs.map(function(child) {
                                return {
                                    field: child.getField(),
                                    value: child.getInternalValue()
                                };
                            })
                        };
                    } else {
                        if (this.value === undefined) {
                            this._getDomValue();
                        }
                        return {
                            type: this.type,
                            value: this.value
                        };
                    }
                }
            }, {
                key: "getLevel",
                value: function getLevel() {
                    return this.parent ? this.parent.getLevel() + 1 : 0;
                }
            }, {
                key: "getNodePath",
                value: function getNodePath() {
                    var path = this.parent ? this.parent.getNodePath() : [];
                    path.push(this);
                    return path;
                }
            }, {
                key: "clone",
                value: function clone() {
                    var clone = new Node(this.editor);
                    clone.type = this.type;
                    clone.field = this.field;
                    clone.fieldInnerText = this.fieldInnerText;
                    clone.fieldEditable = this.fieldEditable;
                    clone.previousField = this.previousField;
                    clone.value = this.value;
                    clone.valueInnerText = this.valueInnerText;
                    clone.previousValue = this.previousValue;
                    clone.expanded = this.expanded;
                    clone.visibleChilds = this.visibleChilds;
                    if (this.childs) {
                        var cloneChilds = [];
                        this.childs.forEach(function(child) {
                            var childClone = child.clone();
                            childClone.setParent(clone);
                            cloneChilds.push(childClone);
                        });
                        clone.childs = cloneChilds;
                    } else {
                        clone.childs = undefined;
                    }
                    return clone;
                }
            }, {
                key: "expand",
                value: function expand(recurse) {
                    if (!this.childs) {
                        return;
                    }
                    this.expanded = true;
                    if (this.dom.expand) {
                        this.dom.expand.className = 'jsoneditor-button jsoneditor-expanded';
                    }
                    this.showChilds();
                    if (recurse !== false) {
                        this.childs.forEach(function(child) {
                            child.expand(recurse);
                        });
                    }
                    this.updateDom({
                        recurse: false
                    });
                }
            }, {
                key: "collapse",
                value: function collapse(recurse) {
                    if (!this.childs) {
                        return;
                    }
                    this.hideChilds();
                    if (recurse !== false) {
                        this.childs.forEach(function(child) {
                            child.collapse(recurse);
                        });
                    }
                    if (this.dom.expand) {
                        this.dom.expand.className = 'jsoneditor-button jsoneditor-collapsed';
                    }
                    this.expanded = false;
                    this.updateDom({
                        recurse: false
                    });
                }
            }, {
                key: "showChilds",
                value: function showChilds() {
                    var childs = this.childs;
                    if (!childs) {
                        return;
                    }
                    if (!this.expanded) {
                        return;
                    }
                    var tr = this.dom.tr;
                    var nextTr;
                    var table = tr ? tr.parentNode : undefined;
                    if (table) {
                        var append = this.getAppendDom();
                        if (!append.parentNode) {
                            nextTr = tr.nextSibling;
                            if (nextTr) {
                                table.insertBefore(append, nextTr);
                            } else {
                                table.appendChild(append);
                            }
                        }
                        var iMax = Math.min(this.childs.length, this.visibleChilds);
                        nextTr = this._getNextTr();
                        for (var i = 0; i < iMax; i++) {
                            var child = this.childs[i];
                            if (!child.getDom().parentNode) {
                                table.insertBefore(child.getDom(), nextTr);
                            }
                            child.showChilds();
                        }
                        var showMore = this.getShowMoreDom();
                        nextTr = this._getNextTr();
                        if (!showMore.parentNode) {
                            table.insertBefore(showMore, nextTr);
                        }
                        this.showMore.updateDom();
                    }
                }
            }, {
                key: "_getNextTr",
                value: function _getNextTr() {
                    if (this.showMore && this.showMore.getDom().parentNode) {
                        return this.showMore.getDom();
                    }
                    if (this.append && this.append.getDom().parentNode) {
                        return this.append.getDom();
                    }
                }
            }, {
                key: "hide",
                value: function hide(options) {
                    var tr = this.dom.tr;
                    var table = tr ? tr.parentNode : undefined;
                    if (table) {
                        table.removeChild(tr);
                    }
                    if (this.dom.popupAnchor) {
                        this.dom.popupAnchor.destroy();
                    }
                    this.hideChilds(options);
                }
            }, {
                key: "hideChilds",
                value: function hideChilds(options) {
                    var childs = this.childs;
                    if (!childs) {
                        return;
                    }
                    if (!this.expanded) {
                        return;
                    }
                    var append = this.getAppendDom();
                    if (append.parentNode) {
                        append.parentNode.removeChild(append);
                    }
                    this.childs.forEach(function(child) {
                        child.hide();
                    });
                    var showMore = this.getShowMoreDom();
                    if (showMore.parentNode) {
                        showMore.parentNode.removeChild(showMore);
                    }
                    if (!options || options.resetVisibleChilds) {
                        this.visibleChilds = this.getMaxVisibleChilds();
                    }
                }
            }, {
                key: "_updateCssClassName",
                value: function _updateCssClassName() {
                    if (this.dom.field && this.editor && this.editor.options && typeof this.editor.options.onClassName === 'function' && this.dom.tree) {
                        Object(util["removeAllClassNames"])(this.dom.tree);
                        var addClasses = this.editor.options.onClassName({
                            path: this.getPath(),
                            field: this.field,
                            value: this.value
                        }) || '';
                        Object(util["addClassName"])(this.dom.tree, 'jsoneditor-values ' + addClasses);
                    }
                }
            }, {
                key: "recursivelyUpdateCssClassesOnNodes",
                value: function recursivelyUpdateCssClassesOnNodes() {
                    this._updateCssClassName();
                    if (Array.isArray(this.childs)) {
                        for (var i = 0; i < this.childs.length; i++) {
                            this.childs[i].recursivelyUpdateCssClassesOnNodes();
                        }
                    }
                }
            }, {
                key: "expandTo",
                value: function expandTo() {
                    var currentNode = this.parent;
                    while (currentNode) {
                        if (!currentNode.expanded) {
                            currentNode.expand();
                        }
                        currentNode = currentNode.parent;
                    }
                }
            }, {
                key: "appendChild",
                value: function appendChild(node, visible, updateDom) {
                    if (this._hasChilds()) {
                        node.setParent(this);
                        node.fieldEditable = this.type === 'object';
                        if (this.type === 'array') {
                            node.index = this.childs.length;
                        }
                        if (this.type === 'object' && node.field === undefined) {
                            node.setField('');
                        }
                        this.childs.push(node);
                        if (this.expanded && visible !== false) {
                            var newTr = node.getDom();
                            var nextTr = this._getNextTr();
                            var table = nextTr ? nextTr.parentNode : undefined;
                            if (nextTr && table) {
                                table.insertBefore(newTr, nextTr);
                            }
                            node.showChilds();
                            this.visibleChilds++;
                        }
                        if (updateDom !== false) {
                            this.updateDom({
                                updateIndexes: true
                            });
                            node.updateDom({
                                recurse: true
                            });
                        }
                    }
                }
            }, {
                key: "moveBefore",
                value: function moveBefore(node, beforeNode, updateDom) {
                    if (this._hasChilds()) {
                        var tbody = this.dom.tr ? this.dom.tr.parentNode : undefined;
                        var trTemp;
                        if (tbody) {
                            trTemp = document.createElement('tr');
                            trTemp.style.height = tbody.clientHeight + 'px';
                            tbody.appendChild(trTemp);
                        }
                        if (node.parent) {
                            node.parent.removeChild(node);
                        }
                        if (beforeNode instanceof Node_AppendNode || !beforeNode) {
                            if (this.childs.length + 1 > this.visibleChilds) {
                                var lastVisibleNode = this.childs[this.visibleChilds - 1];
                                this.insertBefore(node, lastVisibleNode, updateDom);
                            } else {
                                var visible = true;
                                this.appendChild(node, visible, updateDom);
                            }
                        } else {
                            this.insertBefore(node, beforeNode, updateDom);
                        }
                        if (tbody && trTemp) {
                            tbody.removeChild(trTemp);
                        }
                    }
                }
            }, {
                key: "insertBefore",
                value: function insertBefore(node, beforeNode, updateDom) {
                    if (this._hasChilds()) {
                        this.visibleChilds++;
                        if (this.type === 'object' && node.field === undefined) {
                            node.setField('');
                        }
                        if (beforeNode === this.append) {
                            node.setParent(this);
                            node.fieldEditable = this.type === 'object';
                            this.childs.push(node);
                        } else {
                            var index = this.childs.indexOf(beforeNode);
                            if (index === -1) {
                                throw new Error('Node not found');
                            }
                            node.setParent(this);
                            node.fieldEditable = this.type === 'object';
                            this.childs.splice(index, 0, node);
                        }
                        if (this.expanded) {
                            var newTr = node.getDom();
                            var nextTr = beforeNode.getDom();
                            var table = nextTr ? nextTr.parentNode : undefined;
                            if (nextTr && table) {
                                table.insertBefore(newTr, nextTr);
                            }
                            node.showChilds();
                            this.showChilds();
                        }
                        if (updateDom !== false) {
                            this.updateDom({
                                updateIndexes: true
                            });
                            node.updateDom({
                                recurse: true
                            });
                        }
                    }
                }
            }, {
                key: "insertAfter",
                value: function insertAfter(node, afterNode) {
                    if (this._hasChilds()) {
                        var index = this.childs.indexOf(afterNode);
                        var beforeNode = this.childs[index + 1];
                        if (beforeNode) {
                            this.insertBefore(node, beforeNode);
                        } else {
                            this.appendChild(node);
                        }
                    }
                }
            }, {
                key: "search",
                value: function search(text, results) {
                    if (!Array.isArray(results)) {
                        results = [];
                    }
                    var index;
                    var search = text ? text.toLowerCase() : undefined;
                    delete this.searchField;
                    delete this.searchValue;
                    if (this.field !== undefined && results.length <= this.MAX_SEARCH_RESULTS) {
                        var field = String(this.field).toLowerCase();
                        index = field.indexOf(search);
                        if (index !== -1) {
                            this.searchField = true;
                            results.push({
                                node: this,
                                elem: 'field'
                            });
                        }
                        this._updateDomField();
                    }
                    if (this._hasChilds()) {
                        if (this.childs) {
                            this.childs.forEach(function(child) {
                                child.search(text, results);
                            });
                        }
                    } else {
                        if (this.value !== undefined && results.length <= this.MAX_SEARCH_RESULTS) {
                            var value = String(this.value).toLowerCase();
                            index = value.indexOf(search);
                            if (index !== -1) {
                                this.searchValue = true;
                                results.push({
                                    node: this,
                                    elem: 'value'
                                });
                            }
                            this._updateDomValue();
                        }
                    }
                    return results;
                }
            }, {
                key: "scrollTo",
                value: function scrollTo(callback) {
                    this.expandPathToNode();
                    if (this.dom.tr && this.dom.tr.parentNode) {
                        this.editor.scrollTo(this.dom.tr.offsetTop, callback);
                    }
                }
            }, {
                key: "expandPathToNode",
                value: function expandPathToNode() {
                    var node = this;
                    var recurse = false;
                    while (node && node.parent) {
                        var index = node.parent.type === 'array' ? node.index : node.parent.childs.indexOf(node);
                        while (node.parent.visibleChilds < index + 1) {
                            node.parent.visibleChilds += this.getMaxVisibleChilds();
                        }
                        node.parent.expand(recurse);
                        node = node.parent;
                    }
                }
            }, {
                key: "focus",
                value: function focus(elementName) {
                    Node.focusElement = elementName;
                    if (this.dom.tr && this.dom.tr.parentNode) {
                        var dom = this.dom;
                        switch (elementName) {
                        case 'drag':
                            if (dom.drag) {
                                dom.drag.focus();
                            } else {
                                dom.menu.focus();
                            }
                            break;
                        case 'menu':
                            dom.menu.focus();
                            break;
                        case 'expand':
                            if (this._hasChilds()) {
                                dom.expand.focus();
                            } else if (dom.field && this.fieldEditable) {
                                dom.field.focus();
                                Object(util["selectContentEditable"])(dom.field);
                            } else if (dom.value && !this._hasChilds()) {
                                dom.value.focus();
                                Object(util["selectContentEditable"])(dom.value);
                            } else {
                                dom.menu.focus();
                            }
                            break;
                        case 'field':
                            if (dom.field && this.fieldEditable) {
                                dom.field.focus();
                                Object(util["selectContentEditable"])(dom.field);
                            } else if (dom.value && !this._hasChilds()) {
                                dom.value.focus();
                                Object(util["selectContentEditable"])(dom.value);
                            } else if (this._hasChilds()) {
                                dom.expand.focus();
                            } else {
                                dom.menu.focus();
                            }
                            break;
                        case 'value':
                        default:
                            if (dom.select) {
                                dom.select.focus();
                            } else if (dom.value && !this._hasChilds()) {
                                dom.value.focus();
                                Object(util["selectContentEditable"])(dom.value);
                            } else if (dom.field && this.fieldEditable) {
                                dom.field.focus();
                                Object(util["selectContentEditable"])(dom.field);
                            } else if (this._hasChilds()) {
                                dom.expand.focus();
                            } else {
                                dom.menu.focus();
                            }
                            break;
                        }
                    }
                }
            }, {
                key: "containsNode",
                value: function containsNode(node) {
                    if (this === node) {
                        return true;
                    }
                    var childs = this.childs;
                    if (childs) {
                        for (var i = 0, iMax = childs.length; i < iMax; i++) {
                            if (childs[i].containsNode(node)) {
                                return true;
                            }
                        }
                    }
                    return false;
                }
            }, {
                key: "removeChild",
                value: function removeChild(node, updateDom) {
                    if (this.childs) {
                        var index = this.childs.indexOf(node);
                        if (index !== -1) {
                            if (index < this.visibleChilds && this.expanded) {
                                this.visibleChilds--;
                            }
                            node.hide();
                            delete node.searchField;
                            delete node.searchValue;
                            var removedNode = this.childs.splice(index, 1)[0];
                            removedNode.parent = null;
                            if (updateDom !== false) {
                                this.updateDom({
                                    updateIndexes: true
                                });
                            }
                            return removedNode;
                        }
                    }
                    return undefined;
                }
            }, {
                key: "_remove",
                value: function _remove(node) {
                    this.removeChild(node);
                }
            }, {
                key: "changeType",
                value: function changeType(newType) {
                    var oldType = this.type;
                    if (oldType === newType) {
                        return;
                    }
                    if ((newType === 'string' || newType === 'auto') && (oldType === 'string' || oldType === 'auto')) {
                        this.type = newType;
                    } else {
                        var domAnchor = this._detachFromDom();
                        this.clearDom();
                        this.type = newType;
                        if (newType === 'object') {
                            if (!this.childs) {
                                this.childs = [];
                            }
                            this.childs.forEach(function(child) {
                                child.clearDom();
                                delete child.index;
                                child.fieldEditable = true;
                                if (child.field === undefined) {
                                    child.field = '';
                                }
                            });
                            if (oldType === 'string' || oldType === 'auto') {
                                this.expanded = true;
                            }
                        } else if (newType === 'array') {
                            if (!this.childs) {
                                this.childs = [];
                            }
                            this.childs.forEach(function(child, index) {
                                child.clearDom();
                                child.fieldEditable = false;
                                child.index = index;
                            });
                            if (oldType === 'string' || oldType === 'auto') {
                                this.expanded = true;
                            }
                        } else {
                            this.expanded = false;
                        }
                        this._attachToDom(domAnchor);
                    }
                    if (newType === 'auto' || newType === 'string') {
                        if (newType === 'string') {
                            this.value = String(this.value);
                        } else {
                            this.value = Object(util["parseString"])(String(this.value));
                        }
                        this.focus();
                    }
                    this.updateDom({
                        updateIndexes: true
                    });
                }
            }, {
                key: "deepEqual",
                value: function deepEqual(json) {
                    var i;
                    if (this.type === 'array') {
                        if (!Array.isArray(json)) {
                            return false;
                        }
                        if (this.childs.length !== json.length) {
                            return false;
                        }
                        for (i = 0; i < this.childs.length; i++) {
                            if (!this.childs[i].deepEqual(json[i])) {
                                return false;
                            }
                        }
                    } else if (this.type === 'object') {
                        if (_typeof(json) !== 'object' || !json) {
                            return false;
                        }
                        var props = Object.keys(json);
                        if (this.childs.length !== props.length) {
                            return false;
                        }
                        for (i = 0; i < props.length; i++) {
                            var child = this.childs[i];
                            if (child.field !== props[i] || !child.deepEqual(json[child.field])) {
                                return false;
                            }
                        }
                    } else {
                        if (this.value !== json) {
                            return false;
                        }
                    }
                    return true;
                }
            }, {
                key: "_getDomValue",
                value: function _getDomValue() {
                    this._clearValueError();
                    if (this.dom.value && this.type !== 'array' && this.type !== 'object') {
                        this.valueInnerText = Object(util["getInnerText"])(this.dom.value);
                        if (this.valueInnerText === '' && this.dom.value.innerHTML !== '') {
                            this.dom.value.textContent = '';
                        }
                    }
                    if (this.valueInnerText !== undefined) {
                        try {
                            var value;
                            if (this.type === 'string') {
                                value = this._unescapeHTML(this.valueInnerText);
                            } else {
                                var str = this._unescapeHTML(this.valueInnerText);
                                value = Object(util["parseString"])(str);
                            }
                            if (value !== this.value) {
                                this.value = value;
                                this._debouncedOnChangeValue();
                            }
                        } catch (err) {
                            this._setValueError(Object(i18n["c"])('cannotParseValueError'));
                        }
                    }
                }
            }, {
                key: "_setValueError",
                value: function _setValueError(message) {
                    this.valueError = {
                        message: message
                    };
                    this.updateError();
                }
            }, {
                key: "_clearValueError",
                value: function _clearValueError() {
                    if (this.valueError) {
                        this.valueError = null;
                        this.updateError();
                    }
                }
            }, {
                key: "_setFieldError",
                value: function _setFieldError(message) {
                    this.fieldError = {
                        message: message
                    };
                    this.updateError();
                }
            }, {
                key: "_clearFieldError",
                value: function _clearFieldError() {
                    if (this.fieldError) {
                        this.fieldError = null;
                        this.updateError();
                    }
                }
            }, {
                key: "_onChangeValue",
                value: function _onChangeValue() {
                    var oldSelection = this.editor.getDomSelection();
                    if (oldSelection.range) {
                        var undoDiff = Object(util["textDiff"])(String(this.value), String(this.previousValue));
                        oldSelection.range.startOffset = undoDiff.start;
                        oldSelection.range.endOffset = undoDiff.end;
                    }
                    var newSelection = this.editor.getDomSelection();
                    if (newSelection.range) {
                        var redoDiff = Object(util["textDiff"])(String(this.previousValue), String(this.value));
                        newSelection.range.startOffset = redoDiff.start;
                        newSelection.range.endOffset = redoDiff.end;
                    }
                    this.editor._onAction('editValue', {
                        path: this.getInternalPath(),
                        oldValue: this.previousValue,
                        newValue: this.value,
                        oldSelection: oldSelection,
                        newSelection: newSelection
                    });
                    this.previousValue = this.value;
                }
            }, {
                key: "_onChangeField",
                value: function _onChangeField() {
                    var oldSelection = this.editor.getDomSelection();
                    var previous = this.previousField || '';
                    if (oldSelection.range) {
                        var undoDiff = Object(util["textDiff"])(this.field, previous);
                        oldSelection.range.startOffset = undoDiff.start;
                        oldSelection.range.endOffset = undoDiff.end;
                    }
                    var newSelection = this.editor.getDomSelection();
                    if (newSelection.range) {
                        var redoDiff = Object(util["textDiff"])(previous, this.field);
                        newSelection.range.startOffset = redoDiff.start;
                        newSelection.range.endOffset = redoDiff.end;
                    }
                    this.editor._onAction('editField', {
                        parentPath: this.parent.getInternalPath(),
                        index: this.getIndex(),
                        oldValue: this.previousField,
                        newValue: this.field,
                        oldSelection: oldSelection,
                        newSelection: newSelection
                    });
                    this.previousField = this.field;
                }
            }, {
                key: "_updateDomValue",
                value: function _updateDomValue() {
                    var domValue = this.dom.value;
                    if (domValue) {
                        var classNames = ['jsoneditor-value'];
                        var value = this.value;
                        var valueType = this.type === 'auto' ? Object(util["getType"])(value) : this.type;
                        var valueIsUrl = valueType === 'string' && Object(util["isUrl"])(value);
                        classNames.push('jsoneditor-' + valueType);
                        if (valueIsUrl) {
                            classNames.push('jsoneditor-url');
                            tippy(domValue, {
                                content: '<img src="' + domValue.innerHTML + '" width="200" height="100%">',
                                allowHTML: true,
                                placement: 'right'
                            });
                        }
                        var isEmpty = String(this.value) === '' && this.type !== 'array' && this.type !== 'object';
                        if (isEmpty) {
                            classNames.push('jsoneditor-empty');
                        }
                        if (this.searchValueActive) {
                            classNames.push('jsoneditor-highlight-active');
                        }
                        if (this.searchValue) {
                            classNames.push('jsoneditor-highlight');
                        }
                        domValue.className = classNames.join(' ');
                        if (valueType === 'array' || valueType === 'object') {
                            var count = this.childs ? this.childs.length : 0;
                            domValue.title = this.type + ' containing ' + count + ' items';
                        } else if (valueIsUrl && this.editable.value) {
                            domValue.title = Object(i18n["c"])('openUrl');
                        } else {
                            domValue.title = '';
                        }
                        if (valueType === 'boolean' && this.editable.value) {
                            if (!this.dom.checkbox) {
                                this.dom.checkbox = document.createElement('input');
                                this.dom.checkbox.type = 'checkbox';
                                this.dom.tdCheckbox = document.createElement('td');
                                this.dom.tdCheckbox.className = 'jsoneditor-tree';
                                this.dom.tdCheckbox.appendChild(this.dom.checkbox);
                                this.dom.tdValue.parentNode.insertBefore(this.dom.tdCheckbox, this.dom.tdValue);
                            }
                            this.dom.checkbox.checked = this.value;
                        } else {
                            if (this.dom.tdCheckbox) {
                                this.dom.tdCheckbox.parentNode.removeChild(this.dom.tdCheckbox);
                                delete this.dom.tdCheckbox;
                                delete this.dom.checkbox;
                            }
                        }
                        if (this["enum"] && this.editable.value) {
                            if (!this.dom.select) {
                                this.dom.select = document.createElement('select');
                                this.id = this.field + '_' + new Date().getUTCMilliseconds();
                                this.dom.select.id = this.id;
                                this.dom.select.name = this.dom.select.id;
                                this.dom.select.option = document.createElement('option');
                                this.dom.select.option.value = '';
                                this.dom.select.option.textContent = '--';
                                this.dom.select.appendChild(this.dom.select.option);
                                for (var i = 0; i < this["enum"].length; i++) {
                                    this.dom.select.option = document.createElement('option');
                                    this.dom.select.option.value = this["enum"][i];
                                    this.dom.select.option.textContent = this["enum"][i];
                                    if (this.dom.select.option.value === this.value) {
                                        this.dom.select.option.selected = true;
                                    }
                                    this.dom.select.appendChild(this.dom.select.option);
                                }
                                this.dom.tdSelect = document.createElement('td');
                                this.dom.tdSelect.className = 'jsoneditor-tree';
                                this.dom.tdSelect.appendChild(this.dom.select);
                                this.dom.tdValue.parentNode.insertBefore(this.dom.tdSelect, this.dom.tdValue);
                            }
                            if (this.schema && !Node_hasOwnProperty(this.schema, 'oneOf') && !Node_hasOwnProperty(this.schema, 'anyOf') && !Node_hasOwnProperty(this.schema, 'allOf')) {
                                this.valueFieldHTML = this.dom.tdValue.innerHTML;
                                this.dom.tdValue.style.visibility = 'hidden';
                                this.dom.tdValue.textContent = '';
                            } else {
                                delete this.valueFieldHTML;
                            }
                        } else {
                            if (this.dom.tdSelect) {
                                this.dom.tdSelect.parentNode.removeChild(this.dom.tdSelect);
                                delete this.dom.tdSelect;
                                delete this.dom.select;
                                this.dom.tdValue.innerHTML = this.valueFieldHTML;
                                this.dom.tdValue.style.visibility = '';
                                delete this.valueFieldHTML;
                            }
                        }
                        if (this.editable.value && this.editor.options.colorPicker && typeof value === 'string' && Object(util["isValidColor"])(value)) {
                            if (!this.dom.color) {
                                this.dom.color = document.createElement('div');
                                this.dom.color.className = 'jsoneditor-color';
                                this.dom.tdColor = document.createElement('td');
                                this.dom.tdColor.className = 'jsoneditor-tree';
                                this.dom.tdColor.appendChild(this.dom.color);
                                this.dom.tdValue.parentNode.insertBefore(this.dom.tdColor, this.dom.tdValue);
                            }
                            Object(util["addClassName"])(this.dom.value, 'jsoneditor-color-value');
                            this.dom.color.style.backgroundColor = value;
                        } else {
                            this._deleteDomColor();
                        }
                        if (this._showTimestampTag()) {
                            if (!this.dom.date) {
                                this.dom.date = document.createElement('div');
                                this.dom.date.className = 'jsoneditor-date';
                                this.dom.value.parentNode.appendChild(this.dom.date);
                            }
                            var title = null;
                            if (typeof this.editor.options.timestampFormat === 'function') {
                                title = this.editor.options.timestampFormat({
                                    field: this.field,
                                    value: this.value,
                                    path: this.getPath()
                                });
                            }
                            if (!title) {
                                this.dom.date.textContent = new Date(value).toISOString();
                            } else {
                                while (this.dom.date.firstChild) {
                                    this.dom.date.removeChild(this.dom.date.firstChild);
                                }
                                this.dom.date.appendChild(document.createTextNode(title));
                            }
                            this.dom.date.title = new Date(value).toString();
                        } else {
                            if (this.dom.date) {
                                this.dom.date.parentNode.removeChild(this.dom.date);
                                delete this.dom.date;
                            }
                        }
                        Object(util["stripFormatting"])(domValue);
                        this._updateDomDefault();
                    }
                }
            }, {
                key: "_deleteDomColor",
                value: function _deleteDomColor() {
                    if (this.dom.color) {
                        this.dom.tdColor.parentNode.removeChild(this.dom.tdColor);
                        delete this.dom.tdColor;
                        delete this.dom.color;
                        Object(util["removeClassName"])(this.dom.value, 'jsoneditor-color-value');
                    }
                }
            }, {
                key: "_updateDomField",
                value: function _updateDomField() {
                    var domField = this.dom.field;
                    if (domField) {
                        var tooltip = Object(util["makeFieldTooltip"])(this.schema, this.editor.options.language);
                        if (tooltip) {
                            domField.title = tooltip;
                        }
                        var isEmpty = String(this.field) === '' && this.parent && this.parent.type !== 'array';
                        if (isEmpty) {
                            Object(util["addClassName"])(domField, 'jsoneditor-empty');
                        } else {
                            Object(util["removeClassName"])(domField, 'jsoneditor-empty');
                        }
                        if (this.searchFieldActive) {
                            Object(util["addClassName"])(domField, 'jsoneditor-highlight-active');
                        } else {
                            Object(util["removeClassName"])(domField, 'jsoneditor-highlight-active');
                        }
                        if (this.searchField) {
                            Object(util["addClassName"])(domField, 'jsoneditor-highlight');
                        } else {
                            Object(util["removeClassName"])(domField, 'jsoneditor-highlight');
                        }
                        Object(util["stripFormatting"])(domField);
                    }
                }
            }, {
                key: "_getDomField",
                value: function _getDomField(forceUnique) {
                    this._clearFieldError();
                    if (this.dom.field && this.fieldEditable) {
                        this.fieldInnerText = Object(util["getInnerText"])(this.dom.field);
                        if (this.fieldInnerText === '' && this.dom.field.innerHTML !== '') {
                            this.dom.field.textContent = '';
                        }
                    }
                    if (this.fieldInnerText !== undefined) {
                        try {
                            var field = this._unescapeHTML(this.fieldInnerText);
                            var existingFieldNames = this.parent.getFieldNames(this);
                            var isDuplicate = existingFieldNames.indexOf(field) !== -1;
                            if (!isDuplicate) {
                                if (field !== this.field) {
                                    this.field = field;
                                    this._debouncedOnChangeField();
                                }
                            } else {
                                if (forceUnique) {
                                    field = Object(util["findUniqueName"])(field, existingFieldNames);
                                    if (field !== this.field) {
                                        this.field = field;
                                        this._debouncedOnChangeField();
                                    }
                                } else {
                                    this._setFieldError(Object(i18n["c"])('duplicateFieldError'));
                                }
                            }
                        } catch (err) {
                            this._setFieldError(Object(i18n["c"])('cannotParseFieldError'));
                        }
                    }
                }
            }, {
                key: "_updateDomDefault",
                value: function _updateDomDefault() {
                    if (!this.schema || this.schema["default"] === undefined || this._hasChilds()) {
                        return;
                    }
                    var inputElement = this.dom.select ? this.dom.select : this.dom.value;
                    if (!inputElement) {
                        return;
                    }
                    if (this.value === this.schema["default"]) {
                        inputElement.title = Object(i18n["c"])('default');
                        Object(util["addClassName"])(inputElement, 'jsoneditor-is-default');
                        Object(util["removeClassName"])(inputElement, 'jsoneditor-is-not-default');
                    } else {
                        inputElement.removeAttribute('title');
                        Object(util["removeClassName"])(inputElement, 'jsoneditor-is-default');
                        Object(util["addClassName"])(inputElement, 'jsoneditor-is-not-default');
                    }
                }
            }, {
                key: "_showTimestampTag",
                value: function _showTimestampTag() {
                    if (typeof this.value !== 'number') {
                        return false;
                    }
                    var timestampTag = this.editor.options.timestampTag;
                    if (typeof timestampTag === 'function') {
                        var result = timestampTag({
                            field: this.field,
                            value: this.value,
                            path: this.getPath()
                        });
                        if (typeof result === 'boolean') {
                            return result;
                        } else {
                            return Object(util["isTimestamp"])(this.field, this.value);
                        }
                    } else if (timestampTag === true) {
                        return Object(util["isTimestamp"])(this.field, this.value);
                    } else {
                        return false;
                    }
                }
            }, {
                key: "clearDom",
                value: function clearDom() {
                    this.dom = {};
                }
            }, {
                key: "getDom",
                value: function getDom() {
                    var dom = this.dom;
                    if (dom.tr) {
                        return dom.tr;
                    }
                    this._updateEditability();
                    dom.tr = document.createElement('tr');
                    dom.tr.node = this;
                    if (this.editor.options.mode === 'tree') {
                        var tdDrag = document.createElement('td');
                        if (this.editable.field) {
                            if (this.parent) {
                                var domDrag = document.createElement('button');
                                domDrag.type = 'button';
                                dom.drag = domDrag;
                                domDrag.className = 'jsoneditor-button jsoneditor-dragarea';
                                domDrag.title = Object(i18n["c"])('drag');
                                tdDrag.appendChild(domDrag);
                            }
                        }
                        dom.tr.appendChild(tdDrag);
                        var tdMenu = document.createElement('td');
                        var menu = document.createElement('button');
                        menu.type = 'button';
                        dom.menu = menu;
                        menu.className = 'jsoneditor-button jsoneditor-contextmenu-button';
                        menu.title = Object(i18n["c"])('actionsMenu');
                        tdMenu.appendChild(dom.menu);
                        dom.tr.appendChild(tdMenu);
                    }
                    var tdField = document.createElement('td');
                    dom.tr.appendChild(tdField);
                    dom.tree = this._createDomTree();
                    tdField.appendChild(dom.tree);
                    this.updateDom({
                        updateIndexes: true
                    });
                    return dom.tr;
                }
            }, {
                key: "isVisible",
                value: function isVisible() {
                    return this.dom && this.dom.tr && this.dom.tr.parentNode || false;
                }
            }, {
                key: "isDescendantOf",
                value: function isDescendantOf(node) {
                    var n = this.parent;
                    while (n) {
                        if (n === node) {
                            return true;
                        }
                        n = n.parent;
                    }
                    return false;
                }
            }, {
                key: "_createDomField",
                value: function _createDomField() {
                    return document.createElement('div');
                }
            }, {
                key: "setHighlight",
                value: function setHighlight(highlight) {
                    if (this.dom.tr) {
                        if (highlight) {
                            Object(util["addClassName"])(this.dom.tr, 'jsoneditor-highlight');
                        } else {
                            Object(util["removeClassName"])(this.dom.tr, 'jsoneditor-highlight');
                        }
                        if (this.append) {
                            this.append.setHighlight(highlight);
                        }
                        if (this.childs) {
                            this.childs.forEach(function(child) {
                                child.setHighlight(highlight);
                            });
                        }
                    }
                }
            }, {
                key: "setSelected",
                value: function setSelected(selected, isFirst) {
                    this.selected = selected;
                    if (this.dom.tr) {
                        if (selected) {
                            Object(util["addClassName"])(this.dom.tr, 'jsoneditor-selected');
                        } else {
                            Object(util["removeClassName"])(this.dom.tr, 'jsoneditor-selected');
                        }
                        if (isFirst) {
                            Object(util["addClassName"])(this.dom.tr, 'jsoneditor-first');
                        } else {
                            Object(util["removeClassName"])(this.dom.tr, 'jsoneditor-first');
                        }
                        if (this.append) {
                            this.append.setSelected(selected);
                        }
                        if (this.showMore) {
                            this.showMore.setSelected(selected);
                        }
                        if (this.childs) {
                            this.childs.forEach(function(child) {
                                child.setSelected(selected);
                            });
                        }
                    }
                }
            }, {
                key: "updateValue",
                value: function updateValue(value) {
                    this.value = value;
                    this.previousValue = value;
                    this.valueError = undefined;
                    this.updateDom();
                }
            }, {
                key: "updateField",
                value: function updateField(field) {
                    this.field = field;
                    this.previousField = field;
                    this.fieldError = undefined;
                    this.updateDom();
                }
            }, {
                key: "updateDom",
                value: function updateDom(options) {
                    var domTree = this.dom.tree;
                    if (domTree) {
                        domTree.style.marginLeft = this.getLevel() * 24 + 'px';
                    }
                    var domField = this.dom.field;
                    if (domField) {
                        if (this.fieldEditable) {
                            domField.contentEditable = this.editable.field;
                            domField.spellcheck = false;
                            domField.className = 'jsoneditor-field';
                        } else {
                            domField.contentEditable = false;
                            domField.className = 'jsoneditor-readonly';
                        }
                        var fieldText;
                        if (this.index !== undefined) {
                            fieldText = this.index;
                        } else if (this.field !== undefined) {
                            fieldText = this.field;
                        } else {
                            var schema = this.editor.options.schema ? Node._findSchema(this.editor.options.schema, this.editor.options.schemaRefs || {}, this.getPath()) : undefined;
                            if (schema && schema.title) {
                                fieldText = schema.title;
                            } else if (this._hasChilds()) {
                                fieldText = this.type;
                            } else {
                                fieldText = '';
                            }
                        }
                        var escapedField = this._escapeHTML(fieldText);
                        if (document.activeElement !== domField || escapedField !== this._unescapeHTML(Object(util["getInnerText"])(domField))) {
                            domField.innerHTML = escapedField;
                        }
                        this._updateSchema();
                    }
                    var domValue = this.dom.value;
                    if (domValue) {
                        if (this.type === 'array' || this.type === 'object') {
                            this.updateNodeName();
                        } else {
                            var escapedValue = this._escapeHTML(this.value);
                            if (document.activeElement !== domValue || escapedValue !== this._unescapeHTML(Object(util["getInnerText"])(domValue))) {
                                domValue.innerHTML = escapedValue;
                            }
                        }
                    }
                    var tr = this.dom.tr;
                    if (tr) {
                        if (this.type === 'array' || this.type === 'object') {
                            Object(util["addClassName"])(tr, 'jsoneditor-expandable');
                            if (this.expanded) {
                                Object(util["addClassName"])(tr, 'jsoneditor-expanded');
                                Object(util["removeClassName"])(tr, 'jsoneditor-collapsed');
                            } else {
                                Object(util["addClassName"])(tr, 'jsoneditor-collapsed');
                                Object(util["removeClassName"])(tr, 'jsoneditor-expanded');
                            }
                        } else {
                            Object(util["removeClassName"])(tr, 'jsoneditor-expandable');
                            Object(util["removeClassName"])(tr, 'jsoneditor-expanded');
                            Object(util["removeClassName"])(tr, 'jsoneditor-collapsed');
                        }
                    }
                    this._updateDomField();
                    this._updateDomValue();
                    if (options && options.updateIndexes === true) {
                        this._updateDomIndexes();
                    }
                    if (options && options.recurse === true) {
                        if (this.childs) {
                            this.childs.forEach(function(child) {
                                child.updateDom(options);
                            });
                        }
                    }
                    if (this.error) {
                        this.updateError();
                    }
                    if (this.append) {
                        this.append.updateDom();
                    }
                    if (this.showMore) {
                        this.showMore.updateDom();
                    }
                    this._updateCssClassName();
                }
            }, {
                key: "_updateSchema",
                value: function _updateSchema() {
                    if (this.editor && this.editor.options) {
                        this.schema = this.editor.options.schema ? Node._findSchema(this.editor.options.schema, this.editor.options.schemaRefs || {}, this.getPath()) : null;
                        if (this.schema) {
                            this["enum"] = Node._findEnum(this.schema);
                        } else {
                            delete this["enum"];
                        }
                    }
                }
            }, {
                key: "_updateDomIndexes",
                value: function _updateDomIndexes() {
                    var domValue = this.dom.value;
                    var childs = this.childs;
                    if (domValue && childs) {
                        if (this.type === 'array') {
                            childs.forEach(function(child, index) {
                                child.index = index;
                                var childField = child.dom.field;
                                if (childField) {
                                    childField.textContent = index;
                                }
                            });
                        } else if (this.type === 'object') {
                            childs.forEach(function(child) {
                                if (child.index !== undefined) {
                                    delete child.index;
                                    if (child.field === undefined) {
                                        child.field = '';
                                    }
                                }
                            });
                        }
                    }
                }
            }, {
                key: "_createDomValue",
                value: function _createDomValue() {
                    var domValue;
                    if (this.type === 'array') {
                        domValue = document.createElement('div');
                        domValue.textContent = '[...]';
                    } else if (this.type === 'object') {
                        domValue = document.createElement('div');
                        domValue.textContent = '{...}';
                    } else {
                        if (!this.editable.value && Object(util["isUrl"])(this.value)) {
                            domValue = document.createElement('a');
                            domValue.href = this.value;
                            domValue.innerHTML = this._escapeHTML(this.value);
                        } else {
                            domValue = document.createElement('div');
                            domValue.contentEditable = this.editable.value;
                            domValue.spellcheck = false;
                            domValue.innerHTML = this._escapeHTML(this.value);
                        }
                    }
                    return domValue;
                }
            }, {
                key: "_createDomExpandButton",
                value: function _createDomExpandButton() {
                    var expand = document.createElement('button');
                    expand.type = 'button';
                    if (this._hasChilds()) {
                        expand.className = this.expanded ? 'jsoneditor-button jsoneditor-expanded' : 'jsoneditor-button jsoneditor-collapsed';
                        expand.title = Object(i18n["c"])('expandTitle');
                    } else {
                        expand.className = 'jsoneditor-button jsoneditor-invisible';
                        expand.title = '';
                    }
                    return expand;
                }
            }, {
                key: "_createDomTree",
                value: function _createDomTree() {
                    var dom = this.dom;
                    var domTree = document.createElement('table');
                    var tbody = document.createElement('tbody');
                    domTree.style.borderCollapse = 'collapse';
                    domTree.className = 'jsoneditor-values';
                    domTree.appendChild(tbody);
                    var tr = document.createElement('tr');
                    tbody.appendChild(tr);
                    var tdExpand = document.createElement('td');
                    tdExpand.className = 'jsoneditor-tree';
                    tr.appendChild(tdExpand);
                    dom.expand = this._createDomExpandButton();
                    tdExpand.appendChild(dom.expand);
                    dom.tdExpand = tdExpand;
                    var tdField = document.createElement('td');
                    tdField.className = 'jsoneditor-tree';
                    tr.appendChild(tdField);
                    dom.field = this._createDomField();
                    tdField.appendChild(dom.field);
                    dom.tdField = tdField;
                    var tdSeparator = document.createElement('td');
                    tdSeparator.className = 'jsoneditor-tree';
                    tr.appendChild(tdSeparator);
                    if (this.type !== 'object' && this.type !== 'array') {
                        tdSeparator.appendChild(document.createTextNode(':'));
                        tdSeparator.className = 'jsoneditor-separator';
                    }
                    dom.tdSeparator = tdSeparator;
                    var tdValue = document.createElement('td');
                    tdValue.className = 'jsoneditor-tree';
                    tr.appendChild(tdValue);
                    dom.value = this._createDomValue();
                    tdValue.appendChild(dom.value);
                    dom.tdValue = tdValue;
                    return domTree;
                }
            }, {
                key: "onEvent",
                value: function onEvent(event) {
                    var type = event.type;
                    var target = event.target || event.srcElement;
                    var dom = this.dom;
                    var node = this;
                    var expandable = this._hasChilds();
                    if (target === dom.drag || target === dom.menu) {
                        if (type === 'mouseover') {
                            this.editor.highlighter.highlight(this);
                        } else if (type === 'mouseout') {
                            this.editor.highlighter.unhighlight();
                        }
                    }
                    if (type === 'click' && target === dom.menu) {
                        var highlighter = node.editor.highlighter;
                        highlighter.highlight(node);
                        highlighter.lock();
                        Object(util["addClassName"])(dom.menu, 'jsoneditor-selected');
                        this.showContextMenu(dom.menu, function() {
                            Object(util["removeClassName"])(dom.menu, 'jsoneditor-selected');
                            highlighter.unlock();
                            highlighter.unhighlight();
                        });
                    }
                    if (type === 'click') {
                        if (target === dom.expand) {
                            if (expandable) {
                                var recurse = event.ctrlKey;
                                this._onExpand(recurse);
                            }
                        }
                    }
                    if (type === 'click' && (event.target === node.dom.tdColor || event.target === node.dom.color)) {
                        this._showColorPicker();
                    }
                    if (type === 'change' && target === dom.checkbox) {
                        this.dom.value.textContent = String(!this.value);
                        this._getDomValue();
                        this._updateDomDefault();
                    }
                    if (type === 'change' && target === dom.select) {
                        this.dom.value.innerHTML = this._escapeHTML(dom.select.value);
                        this._getDomValue();
                        this._updateDomValue();
                    }
                    var domValue = dom.value;
                    if (target === domValue) {
                        switch (type) {
                        case 'blur':
                        case 'change':
                            {
                                this._getDomValue();
                                this._clearValueError();
                                this._updateDomValue();
                                var escapedValue = this._escapeHTML(this.value);
                                if (escapedValue !== this._unescapeHTML(Object(util["getInnerText"])(domValue))) {
                                    domValue.innerHTML = escapedValue;
                                }
                                break;
                            }
                        case 'input':
                            this._getDomValue();
                            this._updateDomValue();
                            break;
                        case 'keydown':
                        case 'mousedown':
                            this.editor.selection = this.editor.getDomSelection();
                            break;
                        case 'click':
                            if (event.ctrlKey && this.editable.value) {
                                if (Object(util["isUrl"])(this.value)) {
                                    event.preventDefault();
                                    window.open(this.value, '_blank', 'noopener');
                                }
                            }
                            break;
                        case 'keyup':
                            this._getDomValue();
                            this._updateDomValue();
                            break;
                        case 'cut':
                        case 'paste':
                            setTimeout(function() {
                                node._getDomValue();
                                node._updateDomValue();
                            }, 1);
                            break;
                        }
                    }
                    var domField = dom.field;
                    if (target === domField) {
                        switch (type) {
                        case 'blur':
                            {
                                this._getDomField(true);
                                this._updateDomField();
                                var escapedField = this._escapeHTML(this.field);
                                if (escapedField !== this._unescapeHTML(Object(util["getInnerText"])(domField))) {
                                    domField.innerHTML = escapedField;
                                }
                                break;
                            }
                        case 'input':
                            this._getDomField();
                            this._updateSchema();
                            this._updateDomField();
                            this._updateDomValue();
                            break;
                        case 'keydown':
                        case 'mousedown':
                            this.editor.selection = this.editor.getDomSelection();
                            break;
                        case 'keyup':
                            this._getDomField();
                            this._updateDomField();
                            break;
                        case 'cut':
                        case 'paste':
                            setTimeout(function() {
                                node._getDomField();
                                node._updateDomField();
                            }, 1);
                            break;
                        }
                    }
                    var domTree = dom.tree;
                    if (domTree && target === domTree.parentNode && type === 'click' && !event.hasMoved) {
                        var left = event.offsetX !== undefined ? event.offsetX < (this.getLevel() + 1) * 24 : event.pageX < Object(util["getAbsoluteLeft"])(dom.tdSeparator);
                        if (left || expandable) {
                            if (domField) {
                                Object(util["setEndOfContentEditable"])(domField);
                                domField.focus();
                            }
                        } else {
                            if (domValue && !this["enum"]) {
                                Object(util["setEndOfContentEditable"])(domValue);
                                domValue.focus();
                            }
                        }
                    }
                    if ((target === dom.tdExpand && !expandable || target === dom.tdField || target === dom.tdSeparator) && type === 'click' && !event.hasMoved) {
                        if (domField) {
                            Object(util["setEndOfContentEditable"])(domField);
                            domField.focus();
                        }
                    }
                    if (type === 'keydown') {
                        this.onKeyDown(event);
                    }
                    if (typeof this.editor.options.onEvent === 'function') {
                        this._onEvent(event);
                    }
                }
            }, {
                key: "_onEvent",
                value: function _onEvent(event) {
                    var element = event.target;
                    var isField = element === this.dom.field;
                    var isValue = element === this.dom.value || element === this.dom.checkbox || element === this.dom.select;
                    if (isField || isValue) {
                        var info = {
                            field: this.getField(),
                            path: this.getPath()
                        };
                        if (isValue && !this._hasChilds()) {
                            info.value = this.getValue();
                        }
                        this.editor.options.onEvent(info, event);
                    }
                }
            }, {
                key: "onKeyDown",
                value: function onKeyDown(event) {
                    var keynum = event.which || event.keyCode;
                    var target = event.target || event.srcElement;
                    var ctrlKey = event.ctrlKey;
                    var shiftKey = event.shiftKey;
                    var altKey = event.altKey;
                    var handled = false;
                    var prevNode, nextNode, nextDom, nextDom2;
                    var editable = this.editor.options.mode === 'tree';
                    var oldSelection;
                    var oldNextNode;
                    var oldParent;
                    var oldIndexRedo;
                    var newIndexRedo;
                    var oldParentPathRedo;
                    var newParentPathRedo;
                    var nodes;
                    var multiselection;
                    var selectedNodes = this.editor.multiselection.nodes.length > 0 ? this.editor.multiselection.nodes : [this];
                    var firstNode = selectedNodes[0];
                    var lastNode = selectedNodes[selectedNodes.length - 1];
                    if (keynum === 13) {
                        if (target === this.dom.value) {
                            if (!this.editable.value || event.ctrlKey) {
                                if (Object(util["isUrl"])(this.value)) {
                                    window.open(this.value, '_blank', 'noopener');
                                    handled = true;
                                }
                            }
                        } else if (target === this.dom.expand) {
                            var expandable = this._hasChilds();
                            if (expandable) {
                                var recurse = event.ctrlKey;
                                this._onExpand(recurse);
                                target.focus();
                                handled = true;
                            }
                        }
                    } else if (keynum === 68) {
                        if (ctrlKey && editable) {
                            Node.onDuplicate(selectedNodes);
                            handled = true;
                        }
                    } else if (keynum === 69) {
                        if (ctrlKey) {
                            this._onExpand(shiftKey);
                            target.focus();
                            handled = true;
                        }
                    } else if (keynum === 77 && editable) {
                        if (ctrlKey) {
                            this.showContextMenu(target);
                            handled = true;
                        }
                    } else if (keynum === 46 && editable) {
                        if (ctrlKey) {
                            Node.onRemove(selectedNodes);
                            handled = true;
                        }
                    } else if (keynum === 45 && editable) {
                        if (ctrlKey && !shiftKey) {
                            this._onInsertBefore();
                            handled = true;
                        } else if (ctrlKey && shiftKey) {
                            this._onInsertAfter();
                            handled = true;
                        }
                    } else if (keynum === 35) {
                        if (altKey) {
                            var endNode = this._lastNode();
                            if (endNode) {
                                endNode.focus(Node.focusElement || this._getElementName(target));
                            }
                            handled = true;
                        }
                    } else if (keynum === 36) {
                        if (altKey) {
                            var homeNode = this._firstNode();
                            if (homeNode) {
                                homeNode.focus(Node.focusElement || this._getElementName(target));
                            }
                            handled = true;
                        }
                    } else if (keynum === 37) {
                        if (altKey && !shiftKey) {
                            var prevElement = this._previousElement(target);
                            if (prevElement) {
                                this.focus(this._getElementName(prevElement));
                            }
                            handled = true;
                        } else if (altKey && shiftKey && editable) {
                            if (lastNode.expanded) {
                                var appendDom = lastNode.getAppendDom();
                                nextDom = appendDom ? appendDom.nextSibling : undefined;
                            } else {
                                var dom = lastNode.getDom();
                                nextDom = dom.nextSibling;
                            }
                            if (nextDom) {
                                nextNode = Node.getNodeFromTarget(nextDom);
                                nextDom2 = nextDom.nextSibling;
                                var nextNode2 = Node.getNodeFromTarget(nextDom2);
                                if (nextNode && nextNode instanceof Node_AppendNode && !(lastNode.parent.childs.length === 1) && nextNode2 && nextNode2.parent) {
                                    oldSelection = this.editor.getDomSelection();
                                    oldParent = firstNode.parent;
                                    oldNextNode = oldParent.childs[lastNode.getIndex() + 1] || oldParent.append;
                                    oldIndexRedo = firstNode.getIndex();
                                    newIndexRedo = nextNode2.getIndex();
                                    oldParentPathRedo = oldParent.getInternalPath();
                                    newParentPathRedo = nextNode2.parent.getInternalPath();
                                    selectedNodes.forEach(function(node) {
                                        nextNode2.parent.moveBefore(node, nextNode2);
                                    });
                                    this.focus(Node.focusElement || this._getElementName(target));
                                    this.editor._onAction('moveNodes', {
                                        count: selectedNodes.length,
                                        fieldNames: selectedNodes.map(getField),
                                        oldParentPath: oldParent.getInternalPath(),
                                        newParentPath: firstNode.parent.getInternalPath(),
                                        oldIndex: oldNextNode.getIndex(),
                                        newIndex: firstNode.getIndex(),
                                        oldIndexRedo: oldIndexRedo,
                                        newIndexRedo: newIndexRedo,
                                        oldParentPathRedo: oldParentPathRedo,
                                        newParentPathRedo: newParentPathRedo,
                                        oldSelection: oldSelection,
                                        newSelection: this.editor.getDomSelection()
                                    });
                                }
                            }
                        }
                    } else if (keynum === 38) {
                        if (altKey && !shiftKey) {
                            prevNode = this._previousNode();
                            if (prevNode) {
                                this.editor.deselect(true);
                                prevNode.focus(Node.focusElement || this._getElementName(target));
                            }
                            handled = true;
                        } else if (!altKey && ctrlKey && shiftKey && editable) {
                            prevNode = this._previousNode();
                            if (prevNode) {
                                multiselection = this.editor.multiselection;
                                multiselection.start = multiselection.start || this;
                                multiselection.end = prevNode;
                                nodes = this.editor._findTopLevelNodes(multiselection.start, multiselection.end);
                                this.editor.select(nodes);
                                prevNode.focus('field');
                            }
                            handled = true;
                        } else if (altKey && shiftKey && editable) {
                            prevNode = firstNode._previousNode();
                            if (prevNode && prevNode.parent) {
                                oldSelection = this.editor.getDomSelection();
                                oldParent = firstNode.parent;
                                oldNextNode = oldParent.childs[lastNode.getIndex() + 1] || oldParent.append;
                                oldIndexRedo = firstNode.getIndex();
                                newIndexRedo = prevNode.getIndex();
                                oldParentPathRedo = oldParent.getInternalPath();
                                newParentPathRedo = prevNode.parent.getInternalPath();
                                selectedNodes.forEach(function(node) {
                                    prevNode.parent.moveBefore(node, prevNode);
                                });
                                this.focus(Node.focusElement || this._getElementName(target));
                                this.editor._onAction('moveNodes', {
                                    count: selectedNodes.length,
                                    fieldNames: selectedNodes.map(getField),
                                    oldParentPath: oldParent.getInternalPath(),
                                    newParentPath: firstNode.parent.getInternalPath(),
                                    oldIndex: oldNextNode.getIndex(),
                                    newIndex: firstNode.getIndex(),
                                    oldIndexRedo: oldIndexRedo,
                                    newIndexRedo: newIndexRedo,
                                    oldParentPathRedo: oldParentPathRedo,
                                    newParentPathRedo: newParentPathRedo,
                                    oldSelection: oldSelection,
                                    newSelection: this.editor.getDomSelection()
                                });
                            }
                            handled = true;
                        }
                    } else if (keynum === 39) {
                        if (altKey && !shiftKey) {
                            var nextElement = this._nextElement(target);
                            if (nextElement) {
                                this.focus(this._getElementName(nextElement));
                            }
                            handled = true;
                        } else if (altKey && shiftKey && editable) {
                            var _dom = firstNode.getDom();
                            var prevDom = _dom.previousSibling;
                            if (prevDom) {
                                prevNode = Node.getNodeFromTarget(prevDom);
                                if (prevNode && prevNode.parent && !prevNode.isVisible()) {
                                    oldSelection = this.editor.getDomSelection();
                                    oldParent = firstNode.parent;
                                    oldNextNode = oldParent.childs[lastNode.getIndex() + 1] || oldParent.append;
                                    oldIndexRedo = firstNode.getIndex();
                                    newIndexRedo = prevNode.getIndex();
                                    oldParentPathRedo = oldParent.getInternalPath();
                                    newParentPathRedo = prevNode.parent.getInternalPath();
                                    selectedNodes.forEach(function(node) {
                                        prevNode.parent.moveBefore(node, prevNode);
                                    });
                                    this.focus(Node.focusElement || this._getElementName(target));
                                    this.editor._onAction('moveNodes', {
                                        count: selectedNodes.length,
                                        fieldNames: selectedNodes.map(getField),
                                        oldParentPath: oldParent.getInternalPath(),
                                        newParentPath: firstNode.parent.getInternalPath(),
                                        oldIndex: oldNextNode.getIndex(),
                                        newIndex: firstNode.getIndex(),
                                        oldIndexRedo: oldIndexRedo,
                                        newIndexRedo: newIndexRedo,
                                        oldParentPathRedo: oldParentPathRedo,
                                        newParentPathRedo: newParentPathRedo,
                                        oldSelection: oldSelection,
                                        newSelection: this.editor.getDomSelection()
                                    });
                                }
                            }
                        }
                    } else if (keynum === 40) {
                        if (altKey && !shiftKey) {
                            nextNode = this._nextNode();
                            if (nextNode) {
                                this.editor.deselect(true);
                                nextNode.focus(Node.focusElement || this._getElementName(target));
                            }
                            handled = true;
                        } else if (!altKey && ctrlKey && shiftKey && editable) {
                            nextNode = this._nextNode();
                            if (nextNode) {
                                multiselection = this.editor.multiselection;
                                multiselection.start = multiselection.start || this;
                                multiselection.end = nextNode;
                                nodes = this.editor._findTopLevelNodes(multiselection.start, multiselection.end);
                                this.editor.select(nodes);
                                nextNode.focus('field');
                            }
                            handled = true;
                        } else if (altKey && shiftKey && editable) {
                            if (lastNode.expanded) {
                                nextNode = lastNode.append ? lastNode.append._nextNode() : undefined;
                            } else {
                                nextNode = lastNode._nextNode();
                            }
                            if (nextNode && !nextNode.isVisible()) {
                                nextNode = nextNode.parent.showMore;
                            }
                            if (nextNode && nextNode instanceof Node_AppendNode) {
                                nextNode = lastNode;
                            }
                            var _nextNode2 = nextNode && (nextNode._nextNode() || nextNode.parent.append);
                            if (_nextNode2 && _nextNode2.parent) {
                                oldSelection = this.editor.getDomSelection();
                                oldParent = firstNode.parent;
                                oldNextNode = oldParent.childs[lastNode.getIndex() + 1] || oldParent.append;
                                oldIndexRedo = firstNode.getIndex();
                                newIndexRedo = _nextNode2.getIndex();
                                oldParentPathRedo = oldParent.getInternalPath();
                                newParentPathRedo = _nextNode2.parent.getInternalPath();
                                selectedNodes.forEach(function(node) {
                                    _nextNode2.parent.moveBefore(node, _nextNode2);
                                });
                                this.focus(Node.focusElement || this._getElementName(target));
                                this.editor._onAction('moveNodes', {
                                    count: selectedNodes.length,
                                    fieldNames: selectedNodes.map(getField),
                                    oldParentPath: oldParent.getInternalPath(),
                                    newParentPath: firstNode.parent.getInternalPath(),
                                    oldParentPathRedo: oldParentPathRedo,
                                    newParentPathRedo: newParentPathRedo,
                                    oldIndexRedo: oldIndexRedo,
                                    newIndexRedo: newIndexRedo,
                                    oldIndex: oldNextNode.getIndex(),
                                    newIndex: firstNode.getIndex(),
                                    oldSelection: oldSelection,
                                    newSelection: this.editor.getDomSelection()
                                });
                            }
                            handled = true;
                        }
                    }
                    if (handled) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                }
            }, {
                key: "_onExpand",
                value: function _onExpand(recurse) {
                    var table;
                    var frame;
                    var scrollTop;
                    if (recurse) {
                        table = this.dom.tr.parentNode;
                        frame = table.parentNode;
                        scrollTop = frame.scrollTop;
                        frame.removeChild(table);
                    }
                    if (this.expanded) {
                        this.collapse(recurse);
                    } else {
                        this.expand(recurse);
                    }
                    if (recurse) {
                        frame.appendChild(table);
                        frame.scrollTop = scrollTop;
                    }
                }
            }, {
                key: "_showColorPicker",
                value: function _showColorPicker() {
                    if (typeof this.editor.options.onColorPicker === 'function' && this.dom.color) {
                        var node = this;
                        node._deleteDomColor();
                        node.updateDom();
                        var colorAnchor = Object(createAbsoluteAnchor["a"])(this.dom.color, this.editor.getPopupAnchor());
                        this.editor.options.onColorPicker(colorAnchor, this.value, function onChange(value) {
                            if (typeof value === 'string' && value !== node.value) {
                                node._deleteDomColor();
                                node.value = value;
                                node.updateDom();
                                node._debouncedOnChangeValue();
                            }
                        });
                    }
                }
            }, {
                key: "getFieldNames",
                value: function getFieldNames(excludeNode) {
                    if (this.type === 'object') {
                        return this.childs.filter(function(child) {
                            return child !== excludeNode;
                        }).map(function(child) {
                            return child.field;
                        });
                    }
                    return [];
                }
            }, {
                key: "_onInsertBefore",
                value: function _onInsertBefore(field, value, type) {
                    var oldSelection = this.editor.getDomSelection();
                    var newNode = new Node(this.editor,{
                        field: field !== undefined ? field : '',
                        value: value !== undefined ? value : '',
                        type: type
                    });
                    newNode.expand(true);
                    var beforePath = this.getInternalPath();
                    this.parent.insertBefore(newNode, this);
                    this.editor.highlighter.unhighlight();
                    newNode.focus('field');
                    var newSelection = this.editor.getDomSelection();
                    this.editor._onAction('insertBeforeNodes', {
                        nodes: [newNode],
                        paths: [newNode.getInternalPath()],
                        beforePath: beforePath,
                        parentPath: this.parent.getInternalPath(),
                        oldSelection: oldSelection,
                        newSelection: newSelection
                    });
                }
            }, {
                key: "_onInsertAfter",
                value: function _onInsertAfter(field, value, type) {
                    var oldSelection = this.editor.getDomSelection();
                    var newNode = new Node(this.editor,{
                        field: field !== undefined ? field : '',
                        value: value !== undefined ? value : '',
                        type: type
                    });
                    newNode.expand(true);
                    this.parent.insertAfter(newNode, this);
                    this.editor.highlighter.unhighlight();
                    newNode.focus('field');
                    var newSelection = this.editor.getDomSelection();
                    this.editor._onAction('insertAfterNodes', {
                        nodes: [newNode],
                        paths: [newNode.getInternalPath()],
                        afterPath: this.getInternalPath(),
                        parentPath: this.parent.getInternalPath(),
                        oldSelection: oldSelection,
                        newSelection: newSelection
                    });
                }
            }, {
                key: "_onAppend",
                value: function _onAppend(field, value, type) {
                    var oldSelection = this.editor.getDomSelection();
                    var newNode = new Node(this.editor,{
                        field: field !== undefined ? field : '',
                        value: value !== undefined ? value : '',
                        type: type
                    });
                    newNode.expand(true);
                    this.parent.appendChild(newNode);
                    this.editor.highlighter.unhighlight();
                    newNode.focus('field');
                    var newSelection = this.editor.getDomSelection();
                    this.editor._onAction('appendNodes', {
                        nodes: [newNode],
                        paths: [newNode.getInternalPath()],
                        parentPath: this.parent.getInternalPath(),
                        oldSelection: oldSelection,
                        newSelection: newSelection
                    });
                }
            }, {
                key: "_onChangeType",
                value: function _onChangeType(newType) {
                    var oldType = this.type;
                    if (newType !== oldType) {
                        var oldSelection = this.editor.getDomSelection();
                        this.changeType(newType);
                        var newSelection = this.editor.getDomSelection();
                        this.editor._onAction('changeType', {
                            path: this.getInternalPath(),
                            oldType: oldType,
                            newType: newType,
                            oldSelection: oldSelection,
                            newSelection: newSelection
                        });
                    }
                }
            }, {
                key: "sort",
                value: function sort(path, direction) {
                    var triggerAction = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
                    if (typeof path === 'string') {
                        path = Object(util["parsePath"])(path);
                    }
                    if (!this._hasChilds()) {
                        return;
                    }
                    this.hideChilds();
                    var oldChilds = this.childs;
                    this.childs = this.childs.concat();
                    var order = direction === 'desc' ? -1 : 1;
                    if (this.type === 'object') {
                        this.childs.sort(function(a, b) {
                            return order * naturalSort_default()(a.field, b.field);
                        });
                    } else {
                        this.childs.sort(function(a, b) {
                            var nodeA = a.getNestedChild(path);
                            var nodeB = b.getNestedChild(path);
                            if (!nodeA) {
                                return order;
                            }
                            if (!nodeB) {
                                return -order;
                            }
                            var valueA = nodeA.value;
                            var valueB = nodeB.value;
                            if (typeof valueA !== 'string' && typeof valueB !== 'string') {
                                return valueA > valueB ? order : valueA < valueB ? -order : 0;
                            }
                            return order * naturalSort_default()(valueA, valueB);
                        });
                    }
                    this._updateDomIndexes();
                    this.showChilds();
                    if (triggerAction === true) {
                        this.editor._onAction('sort', {
                            path: this.getInternalPath(),
                            oldChilds: oldChilds,
                            newChilds: this.childs
                        });
                    }
                }
            }, {
                key: "update",
                value: function update(newValue) {
                    var oldValue = this.getInternalValue();
                    this.setValue(newValue);
                    this.editor._onAction('transform', {
                        path: this.getInternalPath(),
                        oldValue: oldValue,
                        newValue: this.getInternalValue()
                    });
                }
            }, {
                key: "_detachFromDom",
                value: function _detachFromDom() {
                    var table = this.dom.tr ? this.dom.tr.parentNode : undefined;
                    var lastTr;
                    if (this.expanded) {
                        lastTr = this.getAppendDom();
                    } else {
                        lastTr = this.getDom();
                    }
                    var nextTr = lastTr && lastTr.parentNode ? lastTr.nextSibling : undefined;
                    this.hide({
                        resetVisibleChilds: false
                    });
                    return {
                        table: table,
                        nextTr: nextTr
                    };
                }
            }, {
                key: "_attachToDom",
                value: function _attachToDom(domAnchor) {
                    if (domAnchor.table) {
                        if (domAnchor.nextTr) {
                            domAnchor.table.insertBefore(this.getDom(), domAnchor.nextTr);
                        } else {
                            domAnchor.table.appendChild(this.getDom());
                        }
                    }
                    if (this.expanded) {
                        this.showChilds();
                    }
                }
            }, {
                key: "transform",
                value: function transform(query) {
                    if (!this._hasChilds()) {
                        return;
                    }
                    this.hideChilds();
                    try {
                        var oldInternalValue = this.getInternalValue();
                        var oldValue = this.getValue();
                        var newValue = this.editor.options.executeQuery(oldValue, query);
                        this.setValue(newValue);
                        var newInternalValue = this.getInternalValue();
                        this.editor._onAction('transform', {
                            path: this.getInternalPath(),
                            oldValue: oldInternalValue,
                            newValue: newInternalValue
                        });
                        this.showChilds();
                    } catch (err) {
                        this.showChilds();
                        this.editor._onError(err);
                    }
                }
            }, {
                key: "extract",
                value: function extract() {
                    this.editor.node.hideChilds();
                    this.hideChilds();
                    try {
                        var oldInternalValue = this.editor.node.getInternalValue();
                        this.editor._setRoot(this);
                        var newInternalValue = this.editor.node.getInternalValue();
                        this.editor._onAction('transform', {
                            path: this.editor.node.getInternalPath(),
                            oldValue: oldInternalValue,
                            newValue: newInternalValue
                        });
                    } catch (err) {
                        this.editor._onError(err);
                    } finally {
                        this.updateDom({
                            recurse: true
                        });
                        this.showChilds();
                    }
                }
            }, {
                key: "getNestedChild",
                value: function getNestedChild(path) {
                    var i = 0;
                    var child = this;
                    while (child && i < path.length) {
                        child = child.findChildByProperty(path[i]);
                        i++;
                    }
                    return child;
                }
            }, {
                key: "findChildByProperty",
                value: function findChildByProperty(prop) {
                    if (this.type !== 'object') {
                        return undefined;
                    }
                    return this.childs.find(function(child) {
                        return child.field === prop;
                    });
                }
            }, {
                key: "getAppendDom",
                value: function getAppendDom() {
                    if (!this.append) {
                        this.append = new Node_AppendNode(this.editor);
                        this.append.setParent(this);
                    }
                    return this.append.getDom();
                }
            }, {
                key: "getShowMoreDom",
                value: function getShowMoreDom() {
                    if (!this.showMore) {
                        this.showMore = new Node_ShowMoreNode(this.editor,this);
                    }
                    return this.showMore.getDom();
                }
            }, {
                key: "nextSibling",
                value: function nextSibling() {
                    var index = this.parent.childs.indexOf(this);
                    return this.parent.childs[index + 1] || this.parent.append;
                }
            }, {
                key: "_previousNode",
                value: function _previousNode() {
                    var prevNode = null;
                    var dom = this.getDom();
                    if (dom && dom.parentNode) {
                        var prevDom = dom;
                        do {
                            prevDom = prevDom.previousSibling;
                            prevNode = Node.getNodeFromTarget(prevDom);
                        } while (prevDom && prevNode && prevNode instanceof Node_AppendNode && !prevNode.isVisible());
                    }
                    return prevNode;
                }
            }, {
                key: "_nextNode",
                value: function _nextNode() {
                    var nextNode = null;
                    var dom = this.getDom();
                    if (dom && dom.parentNode) {
                        var nextDom = dom;
                        do {
                            nextDom = nextDom.nextSibling;
                            nextNode = Node.getNodeFromTarget(nextDom);
                        } while (nextDom && nextNode && nextNode instanceof Node_AppendNode && !nextNode.isVisible());
                    }
                    return nextNode;
                }
            }, {
                key: "_firstNode",
                value: function _firstNode() {
                    var firstNode = null;
                    var dom = this.getDom();
                    if (dom && dom.parentNode) {
                        var firstDom = dom.parentNode.firstChild;
                        firstNode = Node.getNodeFromTarget(firstDom);
                    }
                    return firstNode;
                }
            }, {
                key: "_lastNode",
                value: function _lastNode() {
                    var lastNode = null;
                    var dom = this.getDom();
                    if (dom && dom.parentNode) {
                        var lastDom = dom.parentNode.lastChild;
                        lastNode = Node.getNodeFromTarget(lastDom);
                        while (lastDom && lastNode && !lastNode.isVisible()) {
                            lastDom = lastDom.previousSibling;
                            lastNode = Node.getNodeFromTarget(lastDom);
                        }
                    }
                    return lastNode;
                }
            }, {
                key: "_previousElement",
                value: function _previousElement(elem) {
                    var dom = this.dom;
                    switch (elem) {
                    case dom.value:
                        if (this.fieldEditable) {
                            return dom.field;
                        }
                    case dom.field:
                        if (this._hasChilds()) {
                            return dom.expand;
                        }
                    case dom.expand:
                        return dom.menu;
                    case dom.menu:
                        if (dom.drag) {
                            return dom.drag;
                        }
                    default:
                        return null;
                    }
                }
            }, {
                key: "_nextElement",
                value: function _nextElement(elem) {
                    var dom = this.dom;
                    switch (elem) {
                    case dom.drag:
                        return dom.menu;
                    case dom.menu:
                        if (this._hasChilds()) {
                            return dom.expand;
                        }
                    case dom.expand:
                        if (this.fieldEditable) {
                            return dom.field;
                        }
                    case dom.field:
                        if (!this._hasChilds()) {
                            return dom.value;
                        }
                    default:
                        return null;
                    }
                }
            }, {
                key: "_getElementName",
                value: function _getElementName(element) {
                    var _this2 = this;
                    return Object.keys(this.dom).find(function(name) {
                        return _this2.dom[name] === element;
                    });
                }
            }, {
                key: "_hasChilds",
                value: function _hasChilds() {
                    return this.type === 'array' || this.type === 'object';
                }
            }, {
                key: "addTemplates",
                value: function addTemplates(menu, append) {
                    var node = this;
                    var templates = node.editor.options.templates;
                    if (templates == null)
                        return;
                    if (templates.length) {
                        menu.push({
                            type: 'separator'
                        });
                    }
                    var appendData = function appendData(name, data) {
                        node._onAppend(name, data);
                    };
                    var insertData = function insertData(name, data) {
                        node._onInsertBefore(name, data);
                    };
                    templates.forEach(function(template) {
                        menu.push({
                            text: template.text,
                            className: template.className || 'jsoneditor-type-object',
                            title: template.title,
                            click: append ? appendData.bind(this, template.field, template.value) : insertData.bind(this, template.field, template.value)
                        });
                    });
                }
            }, {
                key: "showContextMenu",
                value: function showContextMenu(anchor, onClose) {
                    var node = this;
                    var items = [];
                    if (this.editable.value) {
                        items.push({
                            text: Object(i18n["c"])('type'),
                            title: Object(i18n["c"])('typeTitle'),
                            className: 'jsoneditor-type-' + this.type,
                            submenu: [{
                                text: Object(i18n["c"])('auto'),
                                className: 'jsoneditor-type-auto' + (this.type === 'auto' ? ' jsoneditor-selected' : ''),
                                title: Object(i18n["c"])('autoType'),
                                click: function click() {
                                    node._onChangeType('auto');
                                }
                            }, {
                                text: Object(i18n["c"])('array'),
                                className: 'jsoneditor-type-array' + (this.type === 'array' ? ' jsoneditor-selected' : ''),
                                title: Object(i18n["c"])('arrayType'),
                                click: function click() {
                                    node._onChangeType('array');
                                }
                            }, {
                                text: Object(i18n["c"])('object'),
                                className: 'jsoneditor-type-object' + (this.type === 'object' ? ' jsoneditor-selected' : ''),
                                title: Object(i18n["c"])('objectType'),
                                click: function click() {
                                    node._onChangeType('object');
                                }
                            }, {
                                text: Object(i18n["c"])('string'),
                                className: 'jsoneditor-type-string' + (this.type === 'string' ? ' jsoneditor-selected' : ''),
                                title: Object(i18n["c"])('stringType'),
                                click: function click() {
                                    node._onChangeType('string');
                                }
                            }]
                        });
                    }
                    if (this._hasChilds()) {
                        if (this.editor.options.enableSort) {
                            items.push({
                                text: Object(i18n["c"])('sort'),
                                title: Object(i18n["c"])('sortTitle', {
                                    type: this.type
                                }),
                                className: 'jsoneditor-sort-asc',
                                click: function click() {
                                    node.showSortModal();
                                }
                            });
                        }
                        if (this.editor.options.enableTransform) {
                            items.push({
                                text: Object(i18n["c"])('transform'),
                                title: Object(i18n["c"])('transformTitle', {
                                    type: this.type
                                }),
                                className: 'jsoneditor-transform',
                                click: function click() {
                                    node.showTransformModal();
                                }
                            });
                        }
                        if (this.parent) {
                            items.push({
                                text: Object(i18n["c"])('extract'),
                                title: Object(i18n["c"])('extractTitle', {
                                    type: this.type
                                }),
                                className: 'jsoneditor-extract',
                                click: function click() {
                                    node.extract();
                                }
                            });
                        }
                    }
                    if (this.parent && this.parent._hasChilds()) {
                        if (items.length) {
                            items.push({
                                type: 'separator'
                            });
                        }
                        var childs = node.parent.childs;
                        if (node === childs[childs.length - 1]) {
                            var appendSubmenu = [{
                                text: Object(i18n["c"])('auto'),
                                className: 'jsoneditor-type-auto',
                                title: Object(i18n["c"])('autoType'),
                                click: function click() {
                                    node._onAppend('', '', 'auto');
                                }
                            }, {
                                text: Object(i18n["c"])('array'),
                                className: 'jsoneditor-type-array',
                                title: Object(i18n["c"])('arrayType'),
                                click: function click() {
                                    node._onAppend('', []);
                                }
                            }, {
                                text: Object(i18n["c"])('object'),
                                className: 'jsoneditor-type-object',
                                title: Object(i18n["c"])('objectType'),
                                click: function click() {
                                    node._onAppend('', {});
                                }
                            }, {
                                text: Object(i18n["c"])('string'),
                                className: 'jsoneditor-type-string',
                                title: Object(i18n["c"])('stringType'),
                                click: function click() {
                                    node._onAppend('', '', 'string');
                                }
                            }];
                            node.addTemplates(appendSubmenu, true);
                            items.push({
                                text: Object(i18n["c"])('appendText'),
                                title: Object(i18n["c"])('appendTitle'),
                                submenuTitle: Object(i18n["c"])('appendSubmenuTitle'),
                                className: 'jsoneditor-append',
                                click: function click() {
                                    node._onAppend('', '', 'auto');
                                },
                                submenu: appendSubmenu
                            });
                        }
                        var insertSubmenu = [{
                            text: Object(i18n["c"])('auto'),
                            className: 'jsoneditor-type-auto',
                            title: Object(i18n["c"])('autoType'),
                            click: function click() {
                                node._onInsertBefore('', '', 'auto');
                            }
                        }, {
                            text: Object(i18n["c"])('array'),
                            className: 'jsoneditor-type-array',
                            title: Object(i18n["c"])('arrayType'),
                            click: function click() {
                                node._onInsertBefore('', []);
                            }
                        }, {
                            text: Object(i18n["c"])('object'),
                            className: 'jsoneditor-type-object',
                            title: Object(i18n["c"])('objectType'),
                            click: function click() {
                                node._onInsertBefore('', {});
                            }
                        }, {
                            text: Object(i18n["c"])('string'),
                            className: 'jsoneditor-type-string',
                            title: Object(i18n["c"])('stringType'),
                            click: function click() {
                                node._onInsertBefore('', '', 'string');
                            }
                        }];
                        node.addTemplates(insertSubmenu, false);
                        items.push({
                            text: Object(i18n["c"])('insert'),
                            title: Object(i18n["c"])('insertTitle'),
                            submenuTitle: Object(i18n["c"])('insertSub'),
                            className: 'jsoneditor-insert',
                            click: function click() {
                                node._onInsertBefore('', '', 'auto');
                            },
                            submenu: insertSubmenu
                        });
                        if (this.editable.field) {
                            items.push({
                                text: Object(i18n["c"])('duplicateText'),
                                title: Object(i18n["c"])('duplicateField'),
                                className: 'jsoneditor-duplicate',
                                click: function click() {
                                    Node.onDuplicate(node);
                                }
                            });
                            items.push({
                                text: Object(i18n["c"])('removeText'),
                                title: Object(i18n["c"])('removeField'),
                                className: 'jsoneditor-remove',
                                click: function click() {
                                    Node.onRemove(node);
                                }
                            });
                        }
                    }
                    if (this.editor.options.onCreateMenu) {
                        var path = node.getPath();
                        items = this.editor.options.onCreateMenu(items, {
                            type: 'single',
                            path: path,
                            paths: [path]
                        });
                    }
                    var menu = new ContextMenu["a"](items,{
                        close: onClose
                    });
                    menu.show(anchor, this.editor.getPopupAnchor());
                }
            }, {
                key: "showSortModal",
                value: function showSortModal() {
                    var node = this;
                    var container = this.editor.options.modalAnchor || constants["a"];
                    var json = this.getValue();
                    function onSort(sortedBy) {
                        var path = sortedBy.path;
                        var pathArray = Object(util["parsePath"])(path);
                        node.sortedBy = sortedBy;
                        node.sort(pathArray, sortedBy.direction);
                    }
                    Object(js_showSortModal["showSortModal"])(container, json, onSort, node.sortedBy);
                }
            }, {
                key: "showTransformModal",
                value: function showTransformModal() {
                    var _this3 = this;
                    var _this$editor$options = this.editor.options
                      , modalAnchor = _this$editor$options.modalAnchor
                      , createQuery = _this$editor$options.createQuery
                      , executeQuery = _this$editor$options.executeQuery
                      , queryDescription = _this$editor$options.queryDescription;
                    var json = this.getValue();
                    Object(js_showTransformModal["showTransformModal"])({
                        container: modalAnchor || constants["a"],
                        json: json,
                        queryDescription: queryDescription,
                        createQuery: createQuery,
                        executeQuery: executeQuery,
                        onTransform: function onTransform(query) {
                            _this3.transform(query);
                        }
                    });
                }
            }, {
                key: "_getType",
                value: function _getType(value) {
                    if (value instanceof Array) {
                        return 'array';
                    }
                    if (value instanceof Object) {
                        return 'object';
                    }
                    if (typeof value === 'string' && typeof Object(util["parseString"])(value) !== 'string') {
                        return 'string';
                    }
                    return 'auto';
                }
            }, {
                key: "_escapeHTML",
                value: function _escapeHTML(text) {
                    if (typeof text !== 'string') {
                        return String(text);
                    } else {
                        var htmlEscaped = String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/ {2}/g, ' &nbsp;').replace(/^ /, '&nbsp;').replace(/ $/, '&nbsp;');
                        var json = JSON.stringify(htmlEscaped);
                        var html = json.substring(1, json.length - 1);
                        if (this.editor.options.escapeUnicode === true) {
                            html = Object(util["escapeUnicodeChars"])(html);
                        }
                        return html;
                    }
                }
            }, {
                key: "_unescapeHTML",
                value: function _unescapeHTML(escapedText) {
                    var json = '"' + this._escapeJSON(escapedText) + '"';
                    var htmlEscaped = Object(util["parse"])(json);
                    return htmlEscaped.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&nbsp;|\u00A0/g, ' ').replace(/&amp;/g, '&');
                }
            }, {
                key: "_escapeJSON",
                value: function _escapeJSON(text) {
                    var escaped = '';
                    var i = 0;
                    while (i < text.length) {
                        var c = text.charAt(i);
                        if (c === '\n') {
                            escaped += '\\n';
                        } else if (c === '\\') {
                            escaped += c;
                            i++;
                            c = text.charAt(i);
                            if (c === '' || '"\\/bfnrtu'.indexOf(c) === -1) {
                                escaped += '\\';
                            }
                            escaped += c;
                        } else if (c === '"') {
                            escaped += '\\"';
                        } else {
                            escaped += c;
                        }
                        i++;
                    }
                    return escaped;
                }
            }, {
                key: "updateNodeName",
                value: function updateNodeName() {
                    var count = this.childs ? this.childs.length : 0;
                    var nodeName;
                    if (this.type === 'object' || this.type === 'array') {
                        if (this.editor.options.onNodeName) {
                            try {
                                nodeName = this.editor.options.onNodeName({
                                    path: this.getPath(),
                                    size: count,
                                    type: this.type
                                });
                            } catch (err) {
                                console.error('Error in onNodeName callback: ', err);
                            }
                        }
                        this.dom.value.textContent = this.type === 'object' ? '{' + (nodeName || count) + '}' : '[' + (nodeName || count) + ']';
                    }
                }
            }, {
                key: "recursivelyUpdateNodeName",
                value: function recursivelyUpdateNodeName() {
                    if (this.expanded) {
                        this.updateNodeName();
                        if (this.childs !== 'undefined') {
                            var i;
                            for (i in this.childs) {
                                this.childs[i].recursivelyUpdateNodeName();
                            }
                        }
                    }
                }
            }]);
            return Node;
        }();
        Node_Node.prototype.DEBOUNCE_INTERVAL = 150;
        Node_Node.prototype.MAX_SEARCH_RESULTS = 999;
        var DEFAULT_MAX_VISIBLE_CHILDS = 100;
        Node_Node.focusElement = undefined;
        Node_Node.select = function(editableDiv) {
            setTimeout(function() {
                Object(util["selectContentEditable"])(editableDiv);
            }, 0);
        }
        ;
        Node_Node.onDragStart = function(nodes, event) {
            if (!Array.isArray(nodes)) {
                return Node_Node.onDragStart([nodes], event);
            }
            if (nodes.length === 0) {
                return;
            }
            var firstNode = nodes[0];
            var lastNode = nodes[nodes.length - 1];
            var parent = firstNode.parent;
            var draggedNode = Node_Node.getNodeFromTarget(event.target);
            var editor = firstNode.editor;
            var offsetY = Object(util["getAbsoluteTop"])(draggedNode.dom.tr) - Object(util["getAbsoluteTop"])(firstNode.dom.tr);
            if (!editor.mousemove) {
                editor.mousemove = Object(util["addEventListener"])(event.view, 'mousemove', function(event) {
                    Node_Node.onDrag(nodes, event);
                });
            }
            if (!editor.mouseup) {
                editor.mouseup = Object(util["addEventListener"])(event.view, 'mouseup', function(event) {
                    Node_Node.onDragEnd(nodes, event);
                });
            }
            editor.highlighter.lock();
            editor.drag = {
                oldCursor: document.body.style.cursor,
                oldSelection: editor.getDomSelection(),
                oldPaths: nodes.map(getInternalPath),
                oldParent: parent,
                oldNextNode: parent.childs[lastNode.getIndex() + 1] || parent.append,
                oldParentPathRedo: parent.getInternalPath(),
                oldIndexRedo: firstNode.getIndex(),
                mouseX: event.pageX,
                offsetY: offsetY,
                level: firstNode.getLevel()
            };
            document.body.style.cursor = 'move';
            event.preventDefault();
        }
        ;
        Node_Node.onDrag = function(nodes, event) {
            if (!Array.isArray(nodes)) {
                return Node_Node.onDrag([nodes], event);
            }
            if (nodes.length === 0) {
                return;
            }
            var editor = nodes[0].editor;
            var mouseY = event.pageY - editor.drag.offsetY;
            var mouseX = event.pageX;
            var trPrev, trNext, trFirst, trLast, trRoot;
            var nodePrev, nodeNext;
            var topPrev, topFirst, bottomNext, heightNext;
            var moved = false;
            var firstNode = nodes[0];
            var trThis = firstNode.dom.tr;
            var topThis = Object(util["getAbsoluteTop"])(trThis);
            var heightThis = trThis.offsetHeight;
            if (mouseY < topThis) {
                trPrev = trThis;
                do {
                    trPrev = trPrev.previousSibling;
                    nodePrev = Node_Node.getNodeFromTarget(trPrev);
                    topPrev = trPrev ? Object(util["getAbsoluteTop"])(trPrev) : 0;
                } while (trPrev && mouseY < topPrev);
                if (nodePrev && !nodePrev.parent) {
                    nodePrev = undefined;
                }
                if (!nodePrev) {
                    trRoot = trThis.parentNode.firstChild;
                    trPrev = trRoot ? trRoot.nextSibling : undefined;
                    nodePrev = Node_Node.getNodeFromTarget(trPrev);
                    if (nodePrev === firstNode) {
                        nodePrev = undefined;
                    }
                }
                if (nodePrev && nodePrev.isVisible()) {
                    trPrev = nodePrev.dom.tr;
                    topPrev = trPrev ? Object(util["getAbsoluteTop"])(trPrev) : 0;
                    if (mouseY > topPrev + heightThis) {
                        nodePrev = undefined;
                    }
                }
                if (nodePrev && (editor.options.limitDragging === false || nodePrev.parent === nodes[0].parent)) {
                    nodes.forEach(function(node) {
                        nodePrev.parent.moveBefore(node, nodePrev);
                    });
                    moved = true;
                }
            } else {
                var lastNode = nodes[nodes.length - 1];
                trLast = lastNode.expanded && lastNode.append ? lastNode.append.getDom() : lastNode.dom.tr;
                trFirst = trLast ? trLast.nextSibling : undefined;
                if (trFirst) {
                    topFirst = Object(util["getAbsoluteTop"])(trFirst);
                    trNext = trFirst;
                    do {
                        nodeNext = Node_Node.getNodeFromTarget(trNext);
                        if (trNext) {
                            bottomNext = trNext.nextSibling ? Object(util["getAbsoluteTop"])(trNext.nextSibling) : 0;
                            heightNext = trNext ? bottomNext - topFirst : 0;
                            if (nodeNext && nodeNext.parent.childs.length === nodes.length && nodeNext.parent.childs[nodes.length - 1] === lastNode) {
                                topThis += 27;
                            }
                            trNext = trNext.nextSibling;
                        }
                    } while (trNext && mouseY > topThis + heightNext);
                    if (nodeNext && nodeNext.parent) {
                        var diffX = mouseX - editor.drag.mouseX;
                        var diffLevel = Math.round(diffX / 24 / 2);
                        var level = editor.drag.level + diffLevel;
                        var levelNext = nodeNext.getLevel();
                        trPrev = nodeNext.dom.tr && nodeNext.dom.tr.previousSibling;
                        while (levelNext < level && trPrev) {
                            nodePrev = Node_Node.getNodeFromTarget(trPrev);
                            var isDraggedNode = nodes.some(function(node) {
                                return node === nodePrev || nodePrev.isDescendantOf(node);
                            });
                            if (isDraggedNode) {} else if (nodePrev instanceof Node_AppendNode) {
                                var childs = nodePrev.parent.childs;
                                if (childs.length !== nodes.length || childs[nodes.length - 1] !== lastNode) {
                                    nodeNext = Node_Node.getNodeFromTarget(trPrev);
                                    levelNext = nodeNext.getLevel();
                                } else {
                                    break;
                                }
                            } else {
                                break;
                            }
                            trPrev = trPrev.previousSibling;
                        }
                        if (nodeNext instanceof Node_AppendNode && !nodeNext.isVisible() && nodeNext.parent.showMore.isVisible()) {
                            nodeNext = nodeNext._nextNode();
                        }
                        if (nodeNext && (editor.options.limitDragging === false || nodeNext.parent === nodes[0].parent) && nodeNext.dom.tr && nodeNext.dom.tr !== trLast.nextSibling) {
                            nodes.forEach(function(node) {
                                nodeNext.parent.moveBefore(node, nodeNext);
                            });
                            moved = true;
                        }
                    }
                }
            }
            if (moved) {
                editor.drag.mouseX = mouseX;
                editor.drag.level = firstNode.getLevel();
            }
            editor.startAutoScroll(mouseY);
            event.preventDefault();
        }
        ;
        Node_Node.onDragEnd = function(nodes, event) {
            if (!Array.isArray(nodes)) {
                return Node_Node.onDrag([nodes], event);
            }
            if (nodes.length === 0) {
                return;
            }
            var firstNode = nodes[0];
            var editor = firstNode.editor;
            if (nodes[0]) {
                nodes[0].dom.menu.focus();
            }
            var oldParentPath = editor.drag.oldParent.getInternalPath();
            var newParentPath = firstNode.parent.getInternalPath();
            var sameParent = editor.drag.oldParent === firstNode.parent;
            var oldIndex = editor.drag.oldNextNode.getIndex();
            var newIndex = firstNode.getIndex();
            var oldParentPathRedo = editor.drag.oldParentPathRedo;
            var oldIndexRedo = editor.drag.oldIndexRedo;
            var newIndexRedo = sameParent && oldIndexRedo < newIndex ? newIndex + nodes.length : newIndex;
            if (!sameParent || oldIndexRedo !== newIndex) {
                editor._onAction('moveNodes', {
                    count: nodes.length,
                    fieldNames: nodes.map(getField),
                    oldParentPath: oldParentPath,
                    newParentPath: newParentPath,
                    oldIndex: oldIndex,
                    newIndex: newIndex,
                    oldIndexRedo: oldIndexRedo,
                    newIndexRedo: newIndexRedo,
                    oldParentPathRedo: oldParentPathRedo,
                    newParentPathRedo: null,
                    oldSelection: editor.drag.oldSelection,
                    newSelection: editor.getDomSelection()
                });
            }
            document.body.style.cursor = editor.drag.oldCursor;
            editor.highlighter.unlock();
            nodes.forEach(function(node) {
                node.updateDom();
                if (event.target !== node.dom.drag && event.target !== node.dom.menu) {
                    editor.highlighter.unhighlight();
                }
            });
            delete editor.drag;
            if (editor.mousemove) {
                Object(util["removeEventListener"])(event.view, 'mousemove', editor.mousemove);
                delete editor.mousemove;
            }
            if (editor.mouseup) {
                Object(util["removeEventListener"])(event.view, 'mouseup', editor.mouseup);
                delete editor.mouseup;
            }
            editor.stopAutoScroll();
            event.preventDefault();
        }
        ;
        Node_Node._findEnum = function(schema) {
            if (schema["enum"]) {
                return schema["enum"];
            }
            var composite = schema.oneOf || schema.anyOf || schema.allOf;
            if (composite) {
                var match = composite.filter(function(entry) {
                    return entry["enum"];
                });
                if (match.length > 0) {
                    return match[0]["enum"];
                }
            }
            return null;
        }
        ;
        Node_Node._findSchema = function(topLevelSchema, schemaRefs, path) {
            var currentSchema = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : topLevelSchema;
            var nextPath = path.slice(1, path.length);
            var nextKey = path[0];
            var possibleSchemas = currentSchema.oneOf || currentSchema.anyOf || currentSchema.allOf || [currentSchema];
            var _iterator = _createForOfIteratorHelper(possibleSchemas), _step;
            try {
                for (_iterator.s(); !(_step = _iterator.n()).done; ) {
                    var schema = _step.value;
                    currentSchema = schema;
                    if ('$ref'in currentSchema && typeof currentSchema.$ref === 'string') {
                        var ref = currentSchema.$ref;
                        if (ref in schemaRefs) {
                            currentSchema = schemaRefs[ref];
                        } else if (ref.startsWith('#/')) {
                            var refPath = ref.substring(2).split('/');
                            currentSchema = topLevelSchema;
                            var _iterator2 = _createForOfIteratorHelper(refPath), _step2;
                            try {
                                for (_iterator2.s(); !(_step2 = _iterator2.n()).done; ) {
                                    var segment = _step2.value;
                                    if (segment in currentSchema) {
                                        currentSchema = currentSchema[segment];
                                    } else {
                                        throw Error("Unable to resovle reference ".concat(ref));
                                    }
                                }
                            } catch (err) {
                                _iterator2.e(err);
                            } finally {
                                _iterator2.f();
                            }
                        } else {
                            throw Error("Unable to resolve reference ".concat(ref));
                        }
                    }
                    if (nextKey === undefined) {
                        return currentSchema;
                    }
                    if (typeof nextKey === 'string') {
                        if (_typeof(currentSchema.properties) === 'object' && currentSchema.properties !== null && nextKey in currentSchema.properties) {
                            currentSchema = currentSchema.properties[nextKey];
                            return Node_Node._findSchema(topLevelSchema, schemaRefs, nextPath, currentSchema);
                        }
                        if (_typeof(currentSchema.patternProperties) === 'object' && currentSchema.patternProperties !== null) {
                            for (var prop in currentSchema.patternProperties) {
                                if (nextKey.match(prop)) {
                                    currentSchema = currentSchema.patternProperties[prop];
                                    return Node_Node._findSchema(topLevelSchema, schemaRefs, nextPath, currentSchema);
                                }
                            }
                        }
                        continue;
                    }
                    if (typeof nextKey === 'number' && _typeof(currentSchema.items) === 'object' && currentSchema.items !== null) {
                        currentSchema = currentSchema.items;
                        return Node_Node._findSchema(topLevelSchema, schemaRefs, nextPath, currentSchema);
                    }
                }
            } catch (err) {
                _iterator.e(err);
            } finally {
                _iterator.f();
            }
            return null;
        }
        ;
        Node_Node.onRemove = function(nodes) {
            if (!Array.isArray(nodes)) {
                return Node_Node.onRemove([nodes]);
            }
            if (nodes && nodes.length > 0) {
                var firstNode = nodes[0];
                var parent = firstNode.parent;
                var editor = firstNode.editor;
                var firstIndex = firstNode.getIndex();
                editor.highlighter.unhighlight();
                var oldSelection = editor.getDomSelection();
                Node_Node.blurNodes(nodes);
                var newSelection = editor.getDomSelection();
                var paths = nodes.map(getInternalPath);
                nodes.forEach(function(node) {
                    node.parent._remove(node);
                });
                editor._onAction('removeNodes', {
                    nodes: nodes,
                    paths: paths,
                    parentPath: parent.getInternalPath(),
                    index: firstIndex,
                    oldSelection: oldSelection,
                    newSelection: newSelection
                });
            }
        }
        ;
        Node_Node.onDuplicate = function(nodes) {
            if (!Array.isArray(nodes)) {
                return Node_Node.onDuplicate([nodes]);
            }
            if (nodes && nodes.length > 0) {
                var lastNode = nodes[nodes.length - 1];
                var parent = lastNode.parent;
                var editor = lastNode.editor;
                editor.deselect(editor.multiselection.nodes);
                var oldSelection = editor.getDomSelection();
                var afterNode = lastNode;
                var clones = nodes.map(function(node) {
                    var clone = node.clone();
                    if (node.parent.type === 'object') {
                        var existingFieldNames = node.parent.getFieldNames();
                        clone.field = Object(util["findUniqueName"])(node.field, existingFieldNames);
                    }
                    parent.insertAfter(clone, afterNode);
                    afterNode = clone;
                    return clone;
                });
                if (nodes.length === 1) {
                    if (clones[0].parent.type === 'object') {
                        clones[0].dom.field.innerHTML = nodes[0]._escapeHTML(nodes[0].field);
                        clones[0].focus('field');
                    } else {
                        clones[0].focus();
                    }
                } else {
                    editor.select(clones);
                }
                var newSelection = editor.getDomSelection();
                editor._onAction('duplicateNodes', {
                    paths: nodes.map(getInternalPath),
                    clonePaths: clones.map(getInternalPath),
                    afterPath: lastNode.getInternalPath(),
                    parentPath: parent.getInternalPath(),
                    oldSelection: oldSelection,
                    newSelection: newSelection
                });
            }
        }
        ;
        Node_Node.getNodeFromTarget = function(target) {
            while (target) {
                if (target.node) {
                    return target.node;
                }
                target = target.parentNode;
            }
            return undefined;
        }
        ;
        Node_Node.targetIsColorPicker = function(target) {
            var node = Node_Node.getNodeFromTarget(target);
            if (node) {
                var parent = target && target.parentNode;
                while (parent) {
                    if (parent === node.dom.color) {
                        return true;
                    }
                    parent = parent.parentNode;
                }
            }
            return false;
        }
        ;
        Node_Node.blurNodes = function(nodes) {
            if (!Array.isArray(nodes)) {
                Node_Node.blurNodes([nodes]);
                return;
            }
            var firstNode = nodes[0];
            var parent = firstNode.parent;
            var firstIndex = firstNode.getIndex();
            if (parent.childs[firstIndex + nodes.length]) {
                parent.childs[firstIndex + nodes.length].focus();
            } else if (parent.childs[firstIndex - 1]) {
                parent.childs[firstIndex - 1].focus();
            } else {
                parent.focus();
            }
        }
        ;
        function getInternalPath(node) {
            return node.getInternalPath();
        }
        function getField(node) {
            return node.getField();
        }
        function Node_hasOwnProperty(object, key) {
            return Object.prototype.hasOwnProperty.call(object, key);
        }
        var Node_AppendNode = appendNodeFactory(Node_Node);
        var Node_ShowMoreNode = showMoreNodeFactory(Node_Node);
        function NodeHistory_classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function NodeHistory_defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value"in descriptor)
                    descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function NodeHistory_createClass(Constructor, protoProps, staticProps) {
            if (protoProps)
                NodeHistory_defineProperties(Constructor.prototype, protoProps);
            if (staticProps)
                NodeHistory_defineProperties(Constructor, staticProps);
            return Constructor;
        }
        var NodeHistory_NodeHistory = function() {
            function NodeHistory(editor) {
                NodeHistory_classCallCheck(this, NodeHistory);
                this.editor = editor;
                this.history = [];
                this.index = -1;
                this.clear();
                function findNode(path) {
                    return editor.node.findNodeByInternalPath(path);
                }
                this.actions = {
                    editField: {
                        undo: function undo(params) {
                            var parentNode = findNode(params.parentPath);
                            var node = parentNode.childs[params.index];
                            node.updateField(params.oldValue);
                        },
                        redo: function redo(params) {
                            var parentNode = findNode(params.parentPath);
                            var node = parentNode.childs[params.index];
                            node.updateField(params.newValue);
                        }
                    },
                    editValue: {
                        undo: function undo(params) {
                            findNode(params.path).updateValue(params.oldValue);
                        },
                        redo: function redo(params) {
                            findNode(params.path).updateValue(params.newValue);
                        }
                    },
                    changeType: {
                        undo: function undo(params) {
                            findNode(params.path).changeType(params.oldType);
                        },
                        redo: function redo(params) {
                            findNode(params.path).changeType(params.newType);
                        }
                    },
                    appendNodes: {
                        undo: function undo(params) {
                            var parentNode = findNode(params.parentPath);
                            params.paths.map(findNode).forEach(function(node) {
                                parentNode.removeChild(node);
                            });
                        },
                        redo: function redo(params) {
                            var parentNode = findNode(params.parentPath);
                            params.nodes.forEach(function(node) {
                                parentNode.appendChild(node);
                            });
                        }
                    },
                    insertBeforeNodes: {
                        undo: function undo(params) {
                            var parentNode = findNode(params.parentPath);
                            params.paths.map(findNode).forEach(function(node) {
                                parentNode.removeChild(node);
                            });
                        },
                        redo: function redo(params) {
                            var parentNode = findNode(params.parentPath);
                            var beforeNode = findNode(params.beforePath);
                            params.nodes.forEach(function(node) {
                                parentNode.insertBefore(node, beforeNode);
                            });
                        }
                    },
                    insertAfterNodes: {
                        undo: function undo(params) {
                            var parentNode = findNode(params.parentPath);
                            params.paths.map(findNode).forEach(function(node) {
                                parentNode.removeChild(node);
                            });
                        },
                        redo: function redo(params) {
                            var parentNode = findNode(params.parentPath);
                            var afterNode = findNode(params.afterPath);
                            params.nodes.forEach(function(node) {
                                parentNode.insertAfter(node, afterNode);
                                afterNode = node;
                            });
                        }
                    },
                    removeNodes: {
                        undo: function undo(params) {
                            var parentNode = findNode(params.parentPath);
                            var beforeNode = parentNode.childs[params.index] || parentNode.append;
                            params.nodes.forEach(function(node) {
                                parentNode.insertBefore(node, beforeNode);
                            });
                        },
                        redo: function redo(params) {
                            var parentNode = findNode(params.parentPath);
                            params.paths.map(findNode).forEach(function(node) {
                                parentNode.removeChild(node);
                            });
                        }
                    },
                    duplicateNodes: {
                        undo: function undo(params) {
                            var parentNode = findNode(params.parentPath);
                            params.clonePaths.map(findNode).forEach(function(node) {
                                parentNode.removeChild(node);
                            });
                        },
                        redo: function redo(params) {
                            var parentNode = findNode(params.parentPath);
                            var afterNode = findNode(params.afterPath);
                            var nodes = params.paths.map(findNode);
                            nodes.forEach(function(node) {
                                var clone = node.clone();
                                if (parentNode.type === 'object') {
                                    var existingFieldNames = parentNode.getFieldNames();
                                    clone.field = Object(util["findUniqueName"])(node.field, existingFieldNames);
                                }
                                parentNode.insertAfter(clone, afterNode);
                                afterNode = clone;
                            });
                        }
                    },
                    moveNodes: {
                        undo: function undo(params) {
                            var oldParentNode = findNode(params.oldParentPath);
                            var newParentNode = findNode(params.newParentPath);
                            var oldBeforeNode = oldParentNode.childs[params.oldIndex] || oldParentNode.append;
                            var nodes = newParentNode.childs.slice(params.newIndex, params.newIndex + params.count);
                            nodes.forEach(function(node, index) {
                                node.field = params.fieldNames[index];
                                oldParentNode.moveBefore(node, oldBeforeNode);
                            });
                            if (params.newParentPathRedo === null) {
                                params.newParentPathRedo = newParentNode.getInternalPath();
                            }
                        },
                        redo: function redo(params) {
                            var oldParentNode = findNode(params.oldParentPathRedo);
                            var newParentNode = findNode(params.newParentPathRedo);
                            var newBeforeNode = newParentNode.childs[params.newIndexRedo] || newParentNode.append;
                            var nodes = oldParentNode.childs.slice(params.oldIndexRedo, params.oldIndexRedo + params.count);
                            nodes.forEach(function(node, index) {
                                node.field = params.fieldNames[index];
                                newParentNode.moveBefore(node, newBeforeNode);
                            });
                        }
                    },
                    sort: {
                        undo: function undo(params) {
                            var node = findNode(params.path);
                            node.hideChilds();
                            node.childs = params.oldChilds;
                            node.updateDom({
                                updateIndexes: true
                            });
                            node.showChilds();
                        },
                        redo: function redo(params) {
                            var node = findNode(params.path);
                            node.hideChilds();
                            node.childs = params.newChilds;
                            node.updateDom({
                                updateIndexes: true
                            });
                            node.showChilds();
                        }
                    },
                    transform: {
                        undo: function undo(params) {
                            findNode(params.path).setInternalValue(params.oldValue);
                        },
                        redo: function redo(params) {
                            findNode(params.path).setInternalValue(params.newValue);
                        }
                    }
                };
            }
            NodeHistory_createClass(NodeHistory, [{
                key: "onChange",
                value: function onChange() {}
            }, {
                key: "add",
                value: function add(action, params) {
                    this.index++;
                    this.history[this.index] = {
                        action: action,
                        params: params,
                        timestamp: new Date()
                    };
                    if (this.index < this.history.length - 1) {
                        this.history.splice(this.index + 1, this.history.length - this.index - 1);
                    }
                    this.onChange();
                }
            }, {
                key: "clear",
                value: function clear() {
                    this.history = [];
                    this.index = -1;
                    this.onChange();
                }
            }, {
                key: "canUndo",
                value: function canUndo() {
                    return this.index >= 0;
                }
            }, {
                key: "canRedo",
                value: function canRedo() {
                    return this.index < this.history.length - 1;
                }
            }, {
                key: "undo",
                value: function undo() {
                    if (this.canUndo()) {
                        var obj = this.history[this.index];
                        if (obj) {
                            var action = this.actions[obj.action];
                            if (action && action.undo) {
                                action.undo(obj.params);
                                if (obj.params.oldSelection) {
                                    try {
                                        this.editor.setDomSelection(obj.params.oldSelection);
                                    } catch (err) {
                                        console.error(err);
                                    }
                                }
                            } else {
                                console.error(new Error('unknown action "' + obj.action + '"'));
                            }
                        }
                        this.index--;
                        this.onChange();
                    }
                }
            }, {
                key: "redo",
                value: function redo() {
                    if (this.canRedo()) {
                        this.index++;
                        var obj = this.history[this.index];
                        if (obj) {
                            var action = this.actions[obj.action];
                            if (action && action.redo) {
                                action.redo(obj.params);
                                if (obj.params.newSelection) {
                                    try {
                                        this.editor.setDomSelection(obj.params.newSelection);
                                    } catch (err) {
                                        console.error(err);
                                    }
                                }
                            } else {
                                console.error(new Error('unknown action "' + obj.action + '"'));
                            }
                        }
                        this.onChange();
                    }
                }
            }, {
                key: "destroy",
                value: function destroy() {
                    this.editor = null;
                    this.history = [];
                    this.index = -1;
                }
            }]);
            return NodeHistory;
        }();
        function SearchBox_classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function SearchBox_defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value"in descriptor)
                    descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function SearchBox_createClass(Constructor, protoProps, staticProps) {
            if (protoProps)
                SearchBox_defineProperties(Constructor.prototype, protoProps);
            if (staticProps)
                SearchBox_defineProperties(Constructor, staticProps);
            return Constructor;
        }
        var SearchBox_SearchBox = function() {
            function SearchBox(editor, container) {
                SearchBox_classCallCheck(this, SearchBox);
                var searchBox = this;
                this.editor = editor;
                this.timeout = undefined;
                this.delay = 200;
                this.lastText = undefined;
                this.results = null;
                this.dom = {};
                this.dom.container = container;
                var wrapper = document.createElement('div');
                this.dom.wrapper = wrapper;
                wrapper.className = 'jsoneditor-search';
                container.appendChild(wrapper);
                var results = document.createElement('div');
                this.dom.results = results;
                results.className = 'jsoneditor-results';
                wrapper.appendChild(results);
                var divInput = document.createElement('div');
                this.dom.input = divInput;
                divInput.className = 'jsoneditor-frame';
                divInput.title = Object(i18n["c"])('searchTitle');
                wrapper.appendChild(divInput);
                var refreshSearch = document.createElement('button');
                refreshSearch.type = 'button';
                refreshSearch.className = 'jsoneditor-refresh';
                divInput.appendChild(refreshSearch);
                var search = document.createElement('input');
                search.type = 'text';
                this.dom.search = search;
                search.oninput = function(event) {
                    searchBox._onDelayedSearch(event);
                }
                ;
                search.onchange = function(event) {
                    searchBox._onSearch();
                }
                ;
                search.onkeydown = function(event) {
                    searchBox._onKeyDown(event);
                }
                ;
                search.onkeyup = function(event) {
                    searchBox._onKeyUp(event);
                }
                ;
                refreshSearch.onclick = function(event) {
                    search.select();
                }
                ;
                divInput.appendChild(search);
                var searchNext = document.createElement('button');
                searchNext.type = 'button';
                searchNext.title = Object(i18n["c"])('searchNextResultTitle');
                searchNext.className = 'jsoneditor-next';
                searchNext.onclick = function() {
                    searchBox.next();
                }
                ;
                divInput.appendChild(searchNext);
                var searchPrevious = document.createElement('button');
                searchPrevious.type = 'button';
                searchPrevious.title = Object(i18n["c"])('searchPreviousResultTitle');
                searchPrevious.className = 'jsoneditor-previous';
                searchPrevious.onclick = function() {
                    searchBox.previous();
                }
                ;
                divInput.appendChild(searchPrevious);
            }
            SearchBox_createClass(SearchBox, [{
                key: "next",
                value: function next(focus) {
                    if (this.results) {
                        var index = this.resultIndex !== null ? this.resultIndex + 1 : 0;
                        if (index > this.results.length - 1) {
                            index = 0;
                        }
                        this._setActiveResult(index, focus);
                    }
                }
            }, {
                key: "previous",
                value: function previous(focus) {
                    if (this.results) {
                        var max = this.results.length - 1;
                        var index = this.resultIndex !== null ? this.resultIndex - 1 : max;
                        if (index < 0) {
                            index = max;
                        }
                        this._setActiveResult(index, focus);
                    }
                }
            }, {
                key: "_setActiveResult",
                value: function _setActiveResult(index, focus) {
                    if (this.activeResult) {
                        var prevNode = this.activeResult.node;
                        var prevElem = this.activeResult.elem;
                        if (prevElem === 'field') {
                            delete prevNode.searchFieldActive;
                        } else {
                            delete prevNode.searchValueActive;
                        }
                        prevNode.updateDom();
                    }
                    if (!this.results || !this.results[index]) {
                        this.resultIndex = undefined;
                        this.activeResult = undefined;
                        return;
                    }
                    this.resultIndex = index;
                    var node = this.results[this.resultIndex].node;
                    var elem = this.results[this.resultIndex].elem;
                    if (elem === 'field') {
                        node.searchFieldActive = true;
                    } else {
                        node.searchValueActive = true;
                    }
                    this.activeResult = this.results[this.resultIndex];
                    node.updateDom();
                    node.scrollTo(function() {
                        if (focus) {
                            node.focus(elem);
                        }
                    });
                }
            }, {
                key: "_clearDelay",
                value: function _clearDelay() {
                    if (this.timeout !== undefined) {
                        clearTimeout(this.timeout);
                        delete this.timeout;
                    }
                }
            }, {
                key: "_onDelayedSearch",
                value: function _onDelayedSearch(event) {
                    this._clearDelay();
                    var searchBox = this;
                    this.timeout = setTimeout(function(event) {
                        searchBox._onSearch();
                    }, this.delay);
                }
            }, {
                key: "_onSearch",
                value: function _onSearch(forceSearch) {
                    this._clearDelay();
                    var value = this.dom.search.value;
                    var text = value.length > 0 ? value : undefined;
                    if (text !== this.lastText || forceSearch) {
                        this.lastText = text;
                        this.results = this.editor.search(text);
                        var MAX_SEARCH_RESULTS = this.results[0] ? this.results[0].node.MAX_SEARCH_RESULTS : Infinity;
                        var activeResultIndex = 0;
                        if (this.activeResult) {
                            for (var i = 0; i < this.results.length; i++) {
                                if (this.results[i].node === this.activeResult.node) {
                                    activeResultIndex = i;
                                    break;
                                }
                            }
                        }
                        this._setActiveResult(activeResultIndex, false);
                        if (text !== undefined) {
                            var resultCount = this.results.length;
                            if (resultCount === 0) {
                                this.dom.results.textContent = "no\xA0results";
                            } else if (resultCount === 1) {
                                this.dom.results.textContent = "1\xA0result";
                            } else if (resultCount > MAX_SEARCH_RESULTS) {
                                this.dom.results.textContent = MAX_SEARCH_RESULTS + "+\xA0results";
                            } else {
                                this.dom.results.textContent = resultCount + "\xA0results";
                            }
                        } else {
                            this.dom.results.textContent = '';
                        }
                    }
                }
            }, {
                key: "_onKeyDown",
                value: function _onKeyDown(event) {
                    var keynum = event.which;
                    if (keynum === 27) {
                        this.dom.search.value = '';
                        this._onSearch();
                        event.preventDefault();
                        event.stopPropagation();
                    } else if (keynum === 13) {
                        if (event.ctrlKey) {
                            this._onSearch(true);
                        } else if (event.shiftKey) {
                            this.previous();
                        } else {
                            this.next();
                        }
                        event.preventDefault();
                        event.stopPropagation();
                    }
                }
            }, {
                key: "_onKeyUp",
                value: function _onKeyUp(event) {
                    var keynum = event.keyCode;
                    if (keynum !== 27 && keynum !== 13) {
                        this._onDelayedSearch(event);
                    }
                }
            }, {
                key: "clear",
                value: function clear() {
                    this.dom.search.value = '';
                    this._onSearch();
                }
            }, {
                key: "forceSearch",
                value: function forceSearch() {
                    this._onSearch(true);
                }
            }, {
                key: "isEmpty",
                value: function isEmpty() {
                    return this.dom.search.value === '';
                }
            }, {
                key: "destroy",
                value: function destroy() {
                    this.editor = null;
                    this.dom.container.removeChild(this.dom.wrapper);
                    this.dom = null;
                    this.results = null;
                    this.activeResult = null;
                    this._clearDelay();
                }
            }]);
            return SearchBox;
        }();
        function TreePath_classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function TreePath_defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value"in descriptor)
                    descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function TreePath_createClass(Constructor, protoProps, staticProps) {
            if (protoProps)
                TreePath_defineProperties(Constructor.prototype, protoProps);
            if (staticProps)
                TreePath_defineProperties(Constructor, staticProps);
            return Constructor;
        }
        var TreePath_TreePath = function() {
            function TreePath(container, root) {
                TreePath_classCallCheck(this, TreePath);
                if (container) {
                    this.root = root;
                    this.path = document.createElement('div');
                    this.path.className = 'jsoneditor-treepath';
                    this.path.setAttribute('tabindex', 0);
                    this.contentMenuClicked = false;
                    container.appendChild(this.path);
                    this.reset();
                }
            }
            TreePath_createClass(TreePath, [{
                key: "reset",
                value: function reset() {
                    this.path.textContent = Object(i18n["c"])('selectNode');
                }
            }, {
                key: "setPath",
                value: function setPath(pathObjs) {
                    var me = this;
                    this.path.textContent = '';
                    if (pathObjs && pathObjs.length) {
                        pathObjs.forEach(function(pathObj, idx) {
                            var pathEl = document.createElement('span');
                            var sepEl;
                            pathEl.className = 'jsoneditor-treepath-element';
                            pathEl.innerText = pathObj.name;
                            pathEl.onclick = _onSegmentClick.bind(me, pathObj);
                            me.path.appendChild(pathEl);
                            if (pathObj.children.length) {
                                sepEl = document.createElement('span');
                                sepEl.className = 'jsoneditor-treepath-seperator';
                                sepEl.textContent = "\u25BA";
                                sepEl.onclick = function() {
                                    me.contentMenuClicked = true;
                                    var items = [];
                                    pathObj.children.forEach(function(child) {
                                        items.push({
                                            text: child.name,
                                            className: 'jsoneditor-type-modes' + (pathObjs[idx + 1] + 1 && pathObjs[idx + 1].name === child.name ? ' jsoneditor-selected' : ''),
                                            click: _onContextMenuItemClick.bind(me, pathObj, child.name)
                                        });
                                    });
                                    var menu = new ContextMenu["a"](items,{
                                        limitHeight: true
                                    });
                                    menu.show(sepEl, me.root, true);
                                }
                                ;
                                me.path.appendChild(sepEl);
                            }
                            if (idx === pathObjs.length - 1) {
                                var leftRectPos = (sepEl || pathEl).getBoundingClientRect().right;
                                if (me.path.offsetWidth < leftRectPos) {
                                    me.path.scrollLeft = leftRectPos;
                                }
                                if (me.path.scrollLeft) {
                                    var showAllBtn = document.createElement('span');
                                    showAllBtn.className = 'jsoneditor-treepath-show-all-btn';
                                    showAllBtn.title = 'show all path';
                                    showAllBtn.textContent = '...';
                                    showAllBtn.onclick = _onShowAllClick.bind(me, pathObjs);
                                    me.path.insertBefore(showAllBtn, me.path.firstChild);
                                }
                            }
                        });
                    }
                    function _onShowAllClick(pathObjs) {
                        me.contentMenuClicked = false;
                        Object(util["addClassName"])(me.path, 'show-all');
                        me.path.style.width = me.path.parentNode.getBoundingClientRect().width - 10 + 'px';
                        me.path.onblur = function() {
                            if (me.contentMenuClicked) {
                                me.contentMenuClicked = false;
                                me.path.focus();
                                return;
                            }
                            Object(util["removeClassName"])(me.path, 'show-all');
                            me.path.onblur = undefined;
                            me.path.style.width = '';
                            me.setPath(pathObjs);
                        }
                        ;
                    }
                    function _onSegmentClick(pathObj) {
                        if (this.selectionCallback) {
                            this.selectionCallback(pathObj);
                        }
                    }
                    function _onContextMenuItemClick(pathObj, selection) {
                        if (this.contextMenuCallback) {
                            this.contextMenuCallback(pathObj, selection);
                        }
                    }
                }
            }, {
                key: "onSectionSelected",
                value: function onSectionSelected(callback) {
                    if (typeof callback === 'function') {
                        this.selectionCallback = callback;
                    }
                }
            }, {
                key: "onContextMenuItemSelected",
                value: function onContextMenuItemSelected(callback) {
                    if (typeof callback === 'function') {
                        this.contextMenuCallback = callback;
                    }
                }
            }]);
            return TreePath;
        }();
        var vanilla_picker = __webpack_require__(13);
        var vanilla_picker_default = __webpack_require__.n(vanilla_picker);
        var treemode = {};
        treemode.create = function(container, options) {
            if (!container) {
                throw new Error('No container element provided.');
            }
            this.container = container;
            this.dom = {};
            this.highlighter = new Highlighter();
            this.selection = undefined;
            this.multiselection = {
                nodes: []
            };
            this.validateSchema = null;
            this.validationSequence = 0;
            this.errorNodes = [];
            this.lastSchemaErrors = undefined;
            this.node = null;
            this.focusTarget = null;
            this._setOptions(options);
            if (options.autocomplete) {
                this.autocomplete = autocomplete(options.autocomplete);
            }
            if (this.options.history && this.options.mode !== 'view') {
                this.history = new NodeHistory_NodeHistory(this);
            }
            this._createFrame();
            this._createTable();
        }
        ;
        treemode.destroy = function() {
            if (this.frame && this.container && this.frame.parentNode === this.container) {
                this.container.removeChild(this.frame);
                this.frame = null;
            }
            this.container = null;
            this.dom = null;
            this.clear();
            this.node = null;
            this.focusTarget = null;
            this.selection = null;
            this.multiselection = null;
            this.errorNodes = null;
            this.validateSchema = null;
            this._debouncedValidate = null;
            if (this.history) {
                this.history.destroy();
                this.history = null;
            }
            if (this.searchBox) {
                this.searchBox.destroy();
                this.searchBox = null;
            }
            if (this.modeSwitcher) {
                this.modeSwitcher.destroy();
                this.modeSwitcher = null;
            }
            this.frameFocusTracker.destroy();
        }
        ;
        treemode._setOptions = function(options) {
            var _this = this;
            this.options = {
                search: true,
                history: true,
                mode: 'tree',
                name: undefined,
                schema: null,
                schemaRefs: null,
                autocomplete: null,
                navigationBar: true,
                mainMenuBar: true,
                limitDragging: false,
                onSelectionChange: null,
                colorPicker: true,
                onColorPicker: function onColorPicker(parent, color, onChange) {
                    if (vanilla_picker_default.a) {
                        var pickerHeight = 300;
                        var top = parent.getBoundingClientRect().top;
                        var windowHeight = Object(util["getWindow"])(parent).innerHeight;
                        var showOnTop = windowHeight - top < pickerHeight && top > pickerHeight;
                        new vanilla_picker_default.a({
                            parent: parent,
                            color: color,
                            popup: showOnTop ? 'top' : 'bottom',
                            onDone: function onDone(color) {
                                var alpha = color.rgba[3];
                                var hex = alpha === 1 ? color.hex.substr(0, 7) : color.hex;
                                onChange(hex);
                            }
                        }).show();
                    } else {
                        console.warn('Cannot open color picker: the `vanilla-picker` library is not included in the bundle. ' + 'Either use the full bundle or implement your own color picker using `onColorPicker`.');
                    }
                },
                timestampTag: true,
                timestampFormat: null,
                createQuery: jmespathQuery["a"],
                executeQuery: jmespathQuery["b"],
                onEvent: null,
                enableSort: true,
                enableTransform: true
            };
            if (options) {
                Object.keys(options).forEach(function(prop) {
                    _this.options[prop] = options[prop];
                });
                if (options.limitDragging == null && options.schema != null) {
                    this.options.limitDragging = true;
                }
            }
            this.setSchema(this.options.schema, this.options.schemaRefs);
            this._debouncedValidate = Object(util["debounce"])(this.validate.bind(this), this.DEBOUNCE_INTERVAL);
            if (options.onSelectionChange) {
                this.onSelectionChange(options.onSelectionChange);
            }
            Object(i18n["b"])(this.options.languages);
            Object(i18n["a"])(this.options.language);
        }
        ;
        treemode.set = function(json) {
            if (json instanceof Function || json === undefined) {
                this.clear();
            } else {
                this.content.removeChild(this.table);
                var params = {
                    field: this.options.name,
                    value: json
                };
                var node = new Node_Node(this,params);
                this._setRoot(node);
                this.validate();
                var recurse = false;
                this.node.expand(recurse);
                this.content.appendChild(this.table);
            }
            if (this.history) {
                this.history.clear();
            }
            if (this.searchBox) {
                this.searchBox.clear();
            }
        }
        ;
        treemode.update = function(json) {
            if (this.node.deepEqual(json)) {
                return;
            }
            var selection = this.getSelection();
            this.onChangeDisabled = true;
            this.node.update(json);
            this.onChangeDisabled = false;
            this.validate();
            if (this.searchBox && !this.searchBox.isEmpty()) {
                this.searchBox.forceSearch();
            }
            if (selection && selection.start && selection.end) {
                var startNode = this.node.findNodeByPath(selection.start.path);
                var endNode = this.node.findNodeByPath(selection.end.path);
                if (startNode && endNode) {
                    this.setSelection(selection.start, selection.end);
                } else {
                    this.setSelection({}, {});
                }
            } else {
                this.setSelection({}, {});
            }
        }
        ;
        treemode.get = function() {
            if (this.node) {
                return this.node.getValue();
            } else {
                return undefined;
            }
        }
        ;
        treemode.getText = function() {
            return JSON.stringify(this.get());
        }
        ;
        treemode.setText = function(jsonText) {
            try {
                this.set(Object(util["parse"])(jsonText));
            } catch (err) {
                var repairedJsonText = Object(util["trySimpleJsonRepair"])(jsonText);
                this.set(Object(util["parse"])(repairedJsonText));
            }
        }
        ;
        treemode.updateText = function(jsonText) {
            try {
                this.update(Object(util["parse"])(jsonText));
            } catch (err) {
                var repairJsonText = Object(util["trySimpleJsonRepair"])(jsonText);
                this.update(Object(util["parse"])(repairJsonText));
            }
        }
        ;
        treemode.setName = function(name) {
            this.options.name = name;
            if (this.node) {
                this.node.updateField(this.options.name);
            }
        }
        ;
        treemode.getName = function() {
            return this.options.name;
        }
        ;
        treemode.focus = function() {
            var input = this.scrollableContent.querySelector('[contenteditable=true]');
            if (input) {
                input.focus();
            } else if (this.node.dom.expand) {
                this.node.dom.expand.focus();
            } else if (this.node.dom.menu) {
                this.node.dom.menu.focus();
            } else {
                input = this.frame.querySelector('button');
                if (input) {
                    input.focus();
                }
            }
        }
        ;
        treemode.clear = function() {
            if (this.node) {
                this.node.hide();
                delete this.node;
            }
            if (this.treePath) {
                this.treePath.reset();
            }
        }
        ;
        treemode._setRoot = function(node) {
            this.clear();
            this.node = node;
            node.setParent(null);
            node.setField(this.getName(), false);
            delete node.index;
            this.tbody.appendChild(node.getDom());
        }
        ;
        treemode.search = function(text) {
            var results;
            if (this.node) {
                this.content.removeChild(this.table);
                results = this.node.search(text);
                this.content.appendChild(this.table);
            } else {
                results = [];
            }
            return results;
        }
        ;
        treemode.expandAll = function() {
            if (this.node) {
                this.content.removeChild(this.table);
                this.node.expand();
                this.content.appendChild(this.table);
            }
        }
        ;
        treemode.collapseAll = function() {
            if (this.node) {
                this.content.removeChild(this.table);
                this.node.collapse();
                this.content.appendChild(this.table);
            }
        }
        ;
        treemode._onAction = function(action, params) {
            if (this.history) {
                this.history.add(action, params);
            }
            this._onChange();
        }
        ;
        treemode._onChange = function() {
            if (this.onChangeDisabled) {
                return;
            }
            this.selection = this.getDomSelection();
            this._debouncedValidate();
            if (this.treePath) {
                var selectedNode = this.node && this.selection ? this.node.findNodeByInternalPath(this.selection.path) : this.multiselection ? this.multiselection.nodes[0] : undefined;
                if (selectedNode) {
                    this._updateTreePath(selectedNode.getNodePath());
                } else {
                    this.treePath.reset();
                }
            }
            if (this.options.onChange) {
                try {
                    this.options.onChange();
                } catch (err) {
                    console.error('Error in onChange callback: ', err);
                }
            }
            if (this.options.onChangeJSON) {
                try {
                    this.options.onChangeJSON(this.get());
                } catch (err) {
                    console.error('Error in onChangeJSON callback: ', err);
                }
            }
            if (this.options.onChangeText) {
                try {
                    this.options.onChangeText(this.getText());
                } catch (err) {
                    console.error('Error in onChangeText callback: ', err);
                }
            }
            if (this.options.onClassName) {
                this.node.recursivelyUpdateCssClassesOnNodes();
            }
            if (this.options.onNodeName && this.node.childs) {
                try {
                    this.node.recursivelyUpdateNodeName();
                } catch (err) {
                    console.error('Error in onNodeName callback: ', err);
                }
            }
        }
        ;
        treemode.validate = function() {
            var _this2 = this;
            var root = this.node;
            if (!root) {
                return;
            }
            var json = root.getValue();
            var schemaErrors = [];
            if (this.validateSchema) {
                var valid = this.validateSchema(json);
                if (!valid) {
                    schemaErrors = this.validateSchema.errors.map(function(error) {
                        return Object(util["improveSchemaError"])(error);
                    }).map(function findNode(error) {
                        return {
                            node: root.findNode(error.dataPath),
                            error: error,
                            type: 'validation'
                        };
                    }).filter(function hasNode(entry) {
                        return entry.node != null;
                    });
                }
            }
            try {
                this.validationSequence++;
                var me = this;
                var seq = this.validationSequence;
                this._validateCustom(json).then(function(customValidationErrors) {
                    if (seq === me.validationSequence) {
                        var errorNodes = [].concat(schemaErrors, customValidationErrors || []);
                        me._renderValidationErrors(errorNodes);
                        if (typeof _this2.options.onValidationError === 'function') {
                            if (Object(util["isValidationErrorChanged"])(errorNodes, _this2.lastSchemaErrors)) {
                                _this2.options.onValidationError.call(_this2, errorNodes);
                            }
                            _this2.lastSchemaErrors = errorNodes;
                        }
                    }
                })["catch"](function(err) {
                    console.error(err);
                });
            } catch (err) {
                console.error(err);
            }
        }
        ;
        treemode._renderValidationErrors = function(errorNodes) {
            if (this.errorNodes) {
                this.errorNodes.forEach(function(node) {
                    node.setError(null);
                });
            }
            var parentPairs = errorNodes.reduce(function(all, entry) {
                return entry.node.findParents().filter(function(parent) {
                    return !all.some(function(pair) {
                        return pair[0] === parent;
                    });
                }).map(function(parent) {
                    return [parent, entry.node];
                }).concat(all);
            }, []);
            this.errorNodes = parentPairs.map(function(pair) {
                return {
                    node: pair[0],
                    child: pair[1],
                    error: {
                        message: pair[0].type === 'object' ? Object(i18n["c"])('containsInvalidProperties') : Object(i18n["c"])('containsInvalidItems')
                    }
                };
            }).concat(errorNodes).map(function setError(entry) {
                entry.node.setError(entry.error, entry.child);
                return entry.node;
            });
        }
        ;
        treemode._validateCustom = function(json) {
            try {
                if (this.options.onValidate) {
                    var root = this.node;
                    var customValidateResults = this.options.onValidate(json);
                    var resultPromise = Object(util["isPromise"])(customValidateResults) ? customValidateResults : Promise.resolve(customValidateResults);
                    return resultPromise.then(function(customValidationPathErrors) {
                        if (Array.isArray(customValidationPathErrors)) {
                            return customValidationPathErrors.filter(function(error) {
                                var valid = Object(util["isValidValidationError"])(error);
                                if (!valid) {
                                    console.warn('Ignoring a custom validation error with invalid structure. ' + 'Expected structure: {path: [...], message: "..."}. ' + 'Actual error:', error);
                                }
                                return valid;
                            }).map(function(error) {
                                var node;
                                try {
                                    node = error && error.path ? root.findNodeByPath(error.path) : null;
                                } catch (err) {}
                                if (!node) {
                                    console.warn('Ignoring validation error: node not found. Path:', error.path, 'Error:', error);
                                }
                                return {
                                    node: node,
                                    error: error,
                                    type: 'customValidation'
                                };
                            }).filter(function(entry) {
                                return entry && entry.node && entry.error && entry.error.message;
                            });
                        } else {
                            return null;
                        }
                    });
                }
            } catch (err) {
                return Promise.reject(err);
            }
            return Promise.resolve(null);
        }
        ;
        treemode.refresh = function() {
            if (this.node) {
                this.node.updateDom({
                    recurse: true
                });
            }
        }
        ;
        treemode.startAutoScroll = function(mouseY) {
            var me = this;
            var content = this.scrollableContent;
            var top = Object(util["getAbsoluteTop"])(content);
            var height = content.clientHeight;
            var bottom = top + height;
            var margin = 24;
            var interval = 50;
            if (mouseY < top + margin && content.scrollTop > 0) {
                this.autoScrollStep = (top + margin - mouseY) / 3;
            } else if (mouseY > bottom - margin && height + content.scrollTop < content.scrollHeight) {
                this.autoScrollStep = (bottom - margin - mouseY) / 3;
            } else {
                this.autoScrollStep = undefined;
            }
            if (this.autoScrollStep) {
                if (!this.autoScrollTimer) {
                    this.autoScrollTimer = setInterval(function() {
                        if (me.autoScrollStep) {
                            content.scrollTop -= me.autoScrollStep;
                        } else {
                            me.stopAutoScroll();
                        }
                    }, interval);
                }
            } else {
                this.stopAutoScroll();
            }
        }
        ;
        treemode.stopAutoScroll = function() {
            if (this.autoScrollTimer) {
                clearTimeout(this.autoScrollTimer);
                delete this.autoScrollTimer;
            }
            if (this.autoScrollStep) {
                delete this.autoScrollStep;
            }
        }
        ;
        treemode.setDomSelection = function(selection) {
            if (!selection) {
                return;
            }
            if ('scrollTop'in selection && this.scrollableContent) {
                this.scrollableContent.scrollTop = selection.scrollTop;
            }
            if (selection.paths) {
                var me = this;
                var nodes = selection.paths.map(function(path) {
                    return me.node.findNodeByInternalPath(path);
                });
                this.select(nodes);
            } else {
                var node = selection.path ? this.node.findNodeByInternalPath(selection.path) : null;
                var container = node && selection.domName ? node.dom[selection.domName] : null;
                if (selection.range && container) {
                    var range = Object.assign({}, selection.range, {
                        container: container
                    });
                    Object(util["setSelectionOffset"])(range);
                } else if (node) {
                    node.focus();
                }
            }
        }
        ;
        treemode.getDomSelection = function() {
            var node = Node_Node.getNodeFromTarget(this.focusTarget);
            var focusTarget = this.focusTarget;
            var domName = node ? Object.keys(node.dom).find(function(domName) {
                return node.dom[domName] === focusTarget;
            }) : null;
            var range = Object(util["getSelectionOffset"])();
            if (range && range.container.nodeName !== 'DIV') {
                range = null;
            }
            if (range && range.container !== focusTarget) {
                range = null;
            }
            if (range) {
                delete range.container;
            }
            return {
                path: node ? node.getInternalPath() : null,
                domName: domName,
                range: range,
                paths: this.multiselection.length > 0 ? this.multiselection.nodes.map(function(node) {
                    return node.getInternalPath();
                }) : null,
                scrollTop: this.scrollableContent ? this.scrollableContent.scrollTop : 0
            };
        }
        ;
        treemode.scrollTo = function(top, animateCallback) {
            var content = this.scrollableContent;
            if (content) {
                var editor = this;
                if (editor.animateTimeout) {
                    clearTimeout(editor.animateTimeout);
                    delete editor.animateTimeout;
                }
                if (editor.animateCallback) {
                    editor.animateCallback(false);
                    delete editor.animateCallback;
                }
                var height = content.clientHeight;
                var bottom = content.scrollHeight - height;
                var finalScrollTop = Math.min(Math.max(top - height / 4, 0), bottom);
                var animate = function animate() {
                    var scrollTop = content.scrollTop;
                    var diff = finalScrollTop - scrollTop;
                    if (Math.abs(diff) > 3) {
                        content.scrollTop += diff / 3;
                        editor.animateCallback = animateCallback;
                        editor.animateTimeout = setTimeout(animate, 50);
                    } else {
                        if (animateCallback) {
                            animateCallback(true);
                        }
                        content.scrollTop = finalScrollTop;
                        delete editor.animateTimeout;
                        delete editor.animateCallback;
                    }
                };
                animate();
            } else {
                if (animateCallback) {
                    animateCallback(false);
                }
            }
        }
        ;
        treemode._createFrame = function() {
            this.frame = document.createElement('div');
            this.frame.className = 'jsoneditor jsoneditor-mode-' + this.options.mode;
            this.container.appendChild(this.frame);
            this.contentOuter = document.createElement('div');
            this.contentOuter.className = 'jsoneditor-outer';
            var editor = this;
            function onEvent(event) {
                if (editor._onEvent) {
                    editor._onEvent(event);
                }
            }
            var focusTrackerConfig = {
                target: this.frame,
                onFocus: this.options.onFocus || null,
                onBlur: this.options.onBlur || null
            };
            this.frameFocusTracker = new FocusTracker["a"](focusTrackerConfig);
            this.frame.onclick = function(event) {
                var target = event.target;
                onEvent(event);
                if (target.nodeName === 'BUTTON') {
                    event.preventDefault();
                }
            }
            ;
            this.frame.oninput = onEvent;
            this.frame.onchange = onEvent;
            this.frame.onkeydown = onEvent;
            this.frame.onkeyup = onEvent;
            this.frame.oncut = onEvent;
            this.frame.onpaste = onEvent;
            this.frame.onmousedown = onEvent;
            this.frame.onmouseup = onEvent;
            this.frame.onmouseover = onEvent;
            this.frame.onmouseout = onEvent;
            Object(util["addEventListener"])(this.frame, 'focus', onEvent, true);
            Object(util["addEventListener"])(this.frame, 'blur', onEvent, true);
            this.frame.onfocusin = onEvent;
            this.frame.onfocusout = onEvent;
            if (this.options.mainMenuBar) {
                Object(util["addClassName"])(this.contentOuter, 'has-main-menu-bar');
                this.menu = document.createElement('div');
                this.menu.className = 'jsoneditor-menu';
                this.frame.appendChild(this.menu);
                var expandAll = document.createElement('button');
                expandAll.type = 'button';
                expandAll.className = 'jsoneditor-expand-all';
                expandAll.title = Object(i18n["c"])('expandAll');
                expandAll.onclick = function() {
                    editor.expandAll();
                }
                ;
                this.menu.appendChild(expandAll);
                var collapseAll = document.createElement('button');
                collapseAll.type = 'button';
                collapseAll.title = Object(i18n["c"])('collapseAll');
                collapseAll.className = 'jsoneditor-collapse-all';
                collapseAll.onclick = function() {
                    editor.collapseAll();
                }
                ;
                this.menu.appendChild(collapseAll);
                if (this.options.enableSort) {
                    var sort = document.createElement('button');
                    sort.type = 'button';
                    sort.className = 'jsoneditor-sort';
                    sort.title = Object(i18n["c"])('sortTitleShort');
                    sort.onclick = function() {
                        editor.node.showSortModal();
                    }
                    ;
                    this.menu.appendChild(sort);
                }
                if (this.options.enableTransform) {
                    var transform = document.createElement('button');
                    transform.type = 'button';
                    transform.title = Object(i18n["c"])('transformTitleShort');
                    transform.className = 'jsoneditor-transform';
                    transform.onclick = function() {
                        editor.node.showTransformModal();
                    }
                    ;
                    this.menu.appendChild(transform);
                }
                if (this.history) {
                    var undo = document.createElement('button');
                    undo.type = 'button';
                    undo.className = 'jsoneditor-undo jsoneditor-separator';
                    undo.title = Object(i18n["c"])('undo');
                    undo.onclick = function() {
                        editor._onUndo();
                    }
                    ;
                    this.menu.appendChild(undo);
                    this.dom.undo = undo;
                    var redo = document.createElement('button');
                    redo.type = 'button';
                    redo.className = 'jsoneditor-redo';
                    redo.title = Object(i18n["c"])('redo');
                    redo.onclick = function() {
                        editor._onRedo();
                    }
                    ;
                    this.menu.appendChild(redo);
                    this.dom.redo = redo;
                    this.history.onChange = function() {
                        undo.disabled = !editor.history.canUndo();
                        redo.disabled = !editor.history.canRedo();
                    }
                    ;
                    this.history.onChange();
                }
                if (this.options && this.options.modes && this.options.modes.length) {
                    var me = this;
                    this.modeSwitcher = new ModeSwitcher["a"](this.menu,this.options.modes,this.options.mode,function onSwitch(mode) {
                        me.setMode(mode);
                        me.modeSwitcher.focus();
                    }
                    );
                }
                if (this.options.search) {
                    this.searchBox = new SearchBox_SearchBox(this,this.menu);
                }
            }
            if (this.options.navigationBar) {
                this.navBar = document.createElement('div');
                this.navBar.className = 'jsoneditor-navigation-bar nav-bar-empty';
                this.frame.appendChild(this.navBar);
                this.treePath = new TreePath_TreePath(this.navBar,this.getPopupAnchor());
                this.treePath.onSectionSelected(this._onTreePathSectionSelected.bind(this));
                this.treePath.onContextMenuItemSelected(this._onTreePathMenuItemSelected.bind(this));
            }
        }
        ;
        treemode._onUndo = function() {
            if (this.history) {
                this.history.undo();
                this._onChange();
            }
        }
        ;
        treemode._onRedo = function() {
            if (this.history) {
                this.history.redo();
                this._onChange();
            }
        }
        ;
        treemode._onEvent = function(event) {
            if (Node_Node.targetIsColorPicker(event.target)) {
                return;
            }
            var node = Node_Node.getNodeFromTarget(event.target);
            if (event.type === 'keydown') {
                this._onKeyDown(event);
            }
            if (node && event.type === 'focus') {
                this.focusTarget = event.target;
                if (this.options.autocomplete && this.options.autocomplete.trigger === 'focus') {
                    this._showAutoComplete(event.target);
                }
            }
            if (event.type === 'mousedown') {
                this._startDragDistance(event);
            }
            if (event.type === 'mousemove' || event.type === 'mouseup' || event.type === 'click') {
                this._updateDragDistance(event);
            }
            if (node && this.options && this.options.navigationBar && node && (event.type === 'keydown' || event.type === 'mousedown')) {
                var me = this;
                setTimeout(function() {
                    me._updateTreePath(node.getNodePath());
                });
            }
            if (node && node.selected) {
                if (event.type === 'click') {
                    if (event.target === node.dom.menu) {
                        this.showContextMenu(event.target);
                        return;
                    }
                    if (!event.hasMoved) {
                        this.deselect();
                    }
                }
                if (event.type === 'mousedown') {
                    Node_Node.onDragStart(this.multiselection.nodes, event);
                }
            } else {
                if (event.type === 'mousedown' && Object(util["hasParentNode"])(event.target, this.content)) {
                    this.deselect();
                    if (node && event.target === node.dom.drag) {
                        Node_Node.onDragStart(node, event);
                    } else if (!node || event.target !== node.dom.field && event.target !== node.dom.value && event.target !== node.dom.select) {
                        this._onMultiSelectStart(event);
                    }
                }
            }
            if (node) {
                node.onEvent(event);
            }
        }
        ;
        treemode._updateTreePath = function(pathNodes) {
            if (pathNodes && pathNodes.length) {
                Object(util["removeClassName"])(this.navBar, 'nav-bar-empty');
                var pathObjs = [];
                pathNodes.forEach(function(node) {
                    var pathObj = {
                        name: getName(node),
                        node: node,
                        children: []
                    };
                    if (node.childs && node.childs.length) {
                        node.childs.forEach(function(childNode) {
                            pathObj.children.push({
                                name: getName(childNode),
                                node: childNode
                            });
                        });
                    }
                    pathObjs.push(pathObj);
                });
                this.treePath.setPath(pathObjs);
            } else {
                Object(util["addClassName"])(this.navBar, 'nav-bar-empty');
            }
            function getName(node) {
                return node.parent ? node.parent.type === 'array' ? node.index : node.field : node.field || node.type;
            }
        }
        ;
        treemode._onTreePathSectionSelected = function(pathObj) {
            if (pathObj && pathObj.node) {
                pathObj.node.expandTo();
                pathObj.node.focus();
            }
        }
        ;
        treemode._onTreePathMenuItemSelected = function(pathObj, selection) {
            if (pathObj && pathObj.children.length) {
                var selectionObj = pathObj.children.find(function(obj) {
                    return obj.name === selection;
                });
                if (selectionObj && selectionObj.node) {
                    this._updateTreePath(selectionObj.node.getNodePath());
                    selectionObj.node.expandTo();
                    selectionObj.node.focus();
                }
            }
        }
        ;
        treemode._startDragDistance = function(event) {
            this.dragDistanceEvent = {
                initialTarget: event.target,
                initialPageX: event.pageX,
                initialPageY: event.pageY,
                dragDistance: 0,
                hasMoved: false
            };
        }
        ;
        treemode._updateDragDistance = function(event) {
            if (!this.dragDistanceEvent) {
                this._startDragDistance(event);
            }
            var diffX = event.pageX - this.dragDistanceEvent.initialPageX;
            var diffY = event.pageY - this.dragDistanceEvent.initialPageY;
            this.dragDistanceEvent.dragDistance = Math.sqrt(diffX * diffX + diffY * diffY);
            this.dragDistanceEvent.hasMoved = this.dragDistanceEvent.hasMoved || this.dragDistanceEvent.dragDistance > 10;
            event.dragDistance = this.dragDistanceEvent.dragDistance;
            event.hasMoved = this.dragDistanceEvent.hasMoved;
            return event.dragDistance;
        }
        ;
        treemode._onMultiSelectStart = function(event) {
            var node = Node_Node.getNodeFromTarget(event.target);
            if (this.options.mode !== 'tree' || this.options.onEditable !== undefined) {
                return;
            }
            this.multiselection = {
                start: node || null,
                end: null,
                nodes: []
            };
            this._startDragDistance(event);
            var editor = this;
            if (!this.mousemove) {
                this.mousemove = Object(util["addEventListener"])(event.view, 'mousemove', function(event) {
                    editor._onMultiSelect(event);
                });
            }
            if (!this.mouseup) {
                this.mouseup = Object(util["addEventListener"])(event.view, 'mouseup', function(event) {
                    editor._onMultiSelectEnd(event);
                });
            }
            event.preventDefault();
        }
        ;
        treemode._onMultiSelect = function(event) {
            event.preventDefault();
            this._updateDragDistance(event);
            if (!event.hasMoved) {
                return;
            }
            var node = Node_Node.getNodeFromTarget(event.target);
            if (node) {
                if (this.multiselection.start == null) {
                    this.multiselection.start = node;
                }
                this.multiselection.end = node;
            }
            this.deselect();
            var start = this.multiselection.start;
            var end = this.multiselection.end || this.multiselection.start;
            if (start && end) {
                this.multiselection.nodes = this._findTopLevelNodes(start, end);
                if (this.multiselection.nodes && this.multiselection.nodes.length) {
                    var firstNode = this.multiselection.nodes[0];
                    if (this.multiselection.start === firstNode || this.multiselection.start.isDescendantOf(firstNode)) {
                        this.multiselection.direction = 'down';
                    } else {
                        this.multiselection.direction = 'up';
                    }
                }
                this.select(this.multiselection.nodes);
            }
        }
        ;
        treemode._onMultiSelectEnd = function(event) {
            if (this.multiselection.nodes[0]) {
                this.multiselection.nodes[0].dom.menu.focus();
            }
            this.multiselection.start = null;
            this.multiselection.end = null;
            if (this.mousemove) {
                Object(util["removeEventListener"])(event.view, 'mousemove', this.mousemove);
                delete this.mousemove;
            }
            if (this.mouseup) {
                Object(util["removeEventListener"])(event.view, 'mouseup', this.mouseup);
                delete this.mouseup;
            }
        }
        ;
        treemode.deselect = function(clearStartAndEnd) {
            var selectionChanged = !!this.multiselection.nodes.length;
            this.multiselection.nodes.forEach(function(node) {
                node.setSelected(false);
            });
            this.multiselection.nodes = [];
            if (clearStartAndEnd) {
                this.multiselection.start = null;
                this.multiselection.end = null;
            }
            if (selectionChanged) {
                if (this._selectionChangedHandler) {
                    this._selectionChangedHandler();
                }
            }
        }
        ;
        treemode.select = function(nodes) {
            if (!Array.isArray(nodes)) {
                return this.select([nodes]);
            }
            if (nodes) {
                this.deselect();
                this.multiselection.nodes = nodes.slice(0);
                var first = nodes[0];
                nodes.forEach(function(node) {
                    node.expandPathToNode();
                    node.setSelected(true, node === first);
                });
                if (this._selectionChangedHandler) {
                    var selection = this.getSelection();
                    this._selectionChangedHandler(selection.start, selection.end);
                }
            }
        }
        ;
        treemode._findTopLevelNodes = function(start, end) {
            var startPath = start.getNodePath();
            var endPath = end.getNodePath();
            var i = 0;
            while (i < startPath.length && startPath[i] === endPath[i]) {
                i++;
            }
            var root = startPath[i - 1];
            var startChild = startPath[i];
            var endChild = endPath[i];
            if (!startChild || !endChild) {
                if (root.parent) {
                    startChild = root;
                    endChild = root;
                    root = root.parent;
                } else {
                    startChild = root.childs[0];
                    endChild = root.childs[root.childs.length - 1];
                }
            }
            if (root && startChild && endChild) {
                var startIndex = root.childs.indexOf(startChild);
                var endIndex = root.childs.indexOf(endChild);
                var firstIndex = Math.min(startIndex, endIndex);
                var lastIndex = Math.max(startIndex, endIndex);
                return root.childs.slice(firstIndex, lastIndex + 1);
            } else {
                return [];
            }
        }
        ;
        treemode._showAutoComplete = function(element) {
            var node = Node_Node.getNodeFromTarget(element);
            var jsonElementType = '';
            if (element.className.indexOf('jsoneditor-value') >= 0)
                jsonElementType = 'value';
            if (element.className.indexOf('jsoneditor-field') >= 0)
                jsonElementType = 'field';
            if (jsonElementType === '') {
                return;
            }
            var self = this;
            setTimeout(function() {
                if (node && (self.options.autocomplete.trigger === 'focus' || element.innerText.length > 0)) {
                    var result = self.options.autocomplete.getOptions(element.innerText, node.getPath(), jsonElementType, node.editor);
                    if (result === null) {
                        self.autocomplete.hideDropDown();
                    } else if (typeof result.then === 'function') {
                        result.then(function(obj) {
                            if (obj === null) {
                                self.autocomplete.hideDropDown();
                            } else if (obj.options) {
                                self.autocomplete.show(element, obj.startFrom, obj.options);
                            } else {
                                self.autocomplete.show(element, 0, obj);
                            }
                        })["catch"](function(err) {
                            console.error(err);
                        });
                    } else {
                        if (result.options) {
                            self.autocomplete.show(element, result.startFrom, result.options);
                        } else {
                            self.autocomplete.show(element, 0, result);
                        }
                    }
                } else {
                    self.autocomplete.hideDropDown();
                }
            }, 50);
        }
        ;
        treemode._onKeyDown = function(event) {
            var keynum = event.which || event.keyCode;
            var altKey = event.altKey;
            var ctrlKey = event.ctrlKey;
            var metaKey = event.metaKey;
            var shiftKey = event.shiftKey;
            var handled = false;
            var currentTarget = this.focusTarget;
            if (keynum === 9) {
                var me = this;
                setTimeout(function() {
                    if (me.focusTarget !== currentTarget) {
                        Object(util["selectContentEditable"])(me.focusTarget);
                    }
                }, 0);
            }
            if (this.searchBox) {
                if (ctrlKey && keynum === 70) {
                    this.searchBox.dom.search.focus();
                    this.searchBox.dom.search.select();
                    handled = true;
                } else if (keynum === 114 || ctrlKey && keynum === 71) {
                    var focus = true;
                    if (!shiftKey) {
                        this.searchBox.next(focus);
                    } else {
                        this.searchBox.previous(focus);
                    }
                    handled = true;
                }
            }
            if (this.history) {
                if (ctrlKey && !shiftKey && keynum === 90) {
                    this._onUndo();
                    handled = true;
                } else if (ctrlKey && shiftKey && keynum === 90) {
                    this._onRedo();
                    handled = true;
                }
            }
            if (this.options.autocomplete && !handled) {
                if (!ctrlKey && !altKey && !metaKey && (event.key.length === 1 || keynum === 8 || keynum === 46)) {
                    handled = false;
                    this._showAutoComplete(event.target);
                }
            }
            if (handled) {
                event.preventDefault();
                event.stopPropagation();
            }
        }
        ;
        treemode._createTable = function() {
            if (this.options.navigationBar) {
                Object(util["addClassName"])(this.contentOuter, 'has-nav-bar');
            }
            this.scrollableContent = document.createElement('div');
            this.scrollableContent.className = 'jsoneditor-tree';
            this.contentOuter.appendChild(this.scrollableContent);
            this.content = document.createElement('div');
            this.content.className = 'jsoneditor-tree-inner';
            this.scrollableContent.appendChild(this.content);
            this.table = document.createElement('table');
            this.table.className = 'jsoneditor-tree';
            this.content.appendChild(this.table);
            var col;
            this.colgroupContent = document.createElement('colgroup');
            if (this.options.mode === 'tree') {
                col = document.createElement('col');
                col.width = '24px';
                this.colgroupContent.appendChild(col);
            }
            col = document.createElement('col');
            col.width = '24px';
            this.colgroupContent.appendChild(col);
            col = document.createElement('col');
            this.colgroupContent.appendChild(col);
            this.table.appendChild(this.colgroupContent);
            this.tbody = document.createElement('tbody');
            this.table.appendChild(this.tbody);
            this.frame.appendChild(this.contentOuter);
        }
        ;
        treemode.showContextMenu = function(anchor, onClose) {
            var items = [];
            var selectedNodes = this.multiselection.nodes.slice();
            items.push({
                text: Object(i18n["c"])('duplicateText'),
                title: Object(i18n["c"])('duplicateTitle'),
                className: 'jsoneditor-duplicate',
                click: function click() {
                    Node_Node.onDuplicate(selectedNodes);
                }
            });
            items.push({
                text: Object(i18n["c"])('remove'),
                title: Object(i18n["c"])('removeTitle'),
                className: 'jsoneditor-remove',
                click: function click() {
                    Node_Node.onRemove(selectedNodes);
                }
            });
            if (this.options.onCreateMenu) {
                var paths = selectedNodes.map(function(node) {
                    return node.getPath();
                });
                items = this.options.onCreateMenu(items, {
                    type: 'multiple',
                    path: paths[0],
                    paths: paths
                });
            }
            var menu = new ContextMenu["a"](items,{
                close: onClose
            });
            menu.show(anchor, this.getPopupAnchor());
        }
        ;
        treemode.getPopupAnchor = function() {
            return this.options.popupAnchor || this.frame;
        }
        ;
        treemode.getSelection = function() {
            var selection = {
                start: null,
                end: null
            };
            if (this.multiselection.nodes && this.multiselection.nodes.length) {
                if (this.multiselection.nodes.length) {
                    var selection1 = this.multiselection.nodes[0];
                    var selection2 = this.multiselection.nodes[this.multiselection.nodes.length - 1];
                    if (this.multiselection.direction === 'down') {
                        selection.start = selection1.serialize();
                        selection.end = selection2.serialize();
                    } else {
                        selection.start = selection2.serialize();
                        selection.end = selection1.serialize();
                    }
                }
            }
            return selection;
        }
        ;
        treemode.onSelectionChange = function(callback) {
            if (typeof callback === 'function') {
                this._selectionChangedHandler = Object(util["debounce"])(callback, this.DEBOUNCE_INTERVAL);
            }
        }
        ;
        treemode.setSelection = function(start, end) {
            if (start && start.dom && start.range) {
                console.warn('setSelection/getSelection usage for text selection is deprecated and should not be used, see documentation for supported selection options');
                this.setDomSelection(start);
            }
            var nodes = this._getNodeInstancesByRange(start, end);
            nodes.forEach(function(node) {
                node.expandTo();
            });
            this.select(nodes);
        }
        ;
        treemode._getNodeInstancesByRange = function(start, end) {
            var startNode, endNode;
            if (start && start.path) {
                startNode = this.node.findNodeByPath(start.path);
                if (end && end.path) {
                    endNode = this.node.findNodeByPath(end.path);
                }
            }
            var nodes = [];
            if (startNode instanceof Node_Node) {
                if (endNode instanceof Node_Node && endNode !== startNode) {
                    if (startNode.parent === endNode.parent) {
                        if (startNode.getIndex() < endNode.getIndex()) {
                            start = startNode;
                            end = endNode;
                        } else {
                            start = endNode;
                            end = startNode;
                        }
                        var current = start;
                        nodes.push(current);
                        do {
                            current = current.nextSibling();
                            nodes.push(current);
                        } while (current && current !== end);
                    } else {
                        nodes = this._findTopLevelNodes(startNode, endNode);
                    }
                } else {
                    nodes.push(startNode);
                }
            }
            return nodes;
        }
        ;
        treemode.getNodesByRange = function(start, end) {
            var nodes = this._getNodeInstancesByRange(start, end);
            var serializableNodes = [];
            nodes.forEach(function(node) {
                serializableNodes.push(node.serialize());
            });
            return serializableNodes;
        }
        ;
        var treeModeMixins = [{
            mode: 'tree',
            mixin: treemode,
            data: 'json'
        }, {
            mode: 'view',
            mixin: treemode,
            data: 'json'
        }, {
            mode: 'form',
            mixin: treemode,
            data: 'json'
        }];
    }
    ), (function(module, __webpack_exports__, __webpack_require__) {
        "use strict";
        __webpack_require__.r(__webpack_exports__);
        __webpack_require__.d(__webpack_exports__, "previewModeMixins", function() {
            return previewModeMixins;
        });
        var simpleJsonRepair_min = __webpack_require__(7);
        var simpleJsonRepair_min_default = __webpack_require__.n(simpleJsonRepair_min);
        var constants = __webpack_require__(2);
        var ErrorTable = __webpack_require__(15);
        var FocusTracker = __webpack_require__(8);
        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) {
                throw new TypeError("Cannot call a class as a function");
            }
        }
        function _defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value"in descriptor)
                    descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }
        function _createClass(Constructor, protoProps, staticProps) {
            if (protoProps)
                _defineProperties(Constructor.prototype, protoProps);
            if (staticProps)
                _defineProperties(Constructor, staticProps);
            return Constructor;
        }
        var History = function() {
            function History(onChange, calculateItemSize, limit) {
                _classCallCheck(this, History);
                this.onChange = onChange;
                this.calculateItemSize = calculateItemSize || function() {
                    return 1;
                }
                ;
                this.limit = limit;
                this.items = [];
                this.index = -1;
            }
            _createClass(History, [{
                key: "add",
                value: function add(item) {
                    while (this._calculateHistorySize() > this.limit && this.items.length > 1) {
                        this.items.shift();
                        this.index--;
                    }
                    this.items = this.items.slice(0, this.index + 1);
                    this.items.push(item);
                    this.index++;
                    this.onChange();
                }
            }, {
                key: "_calculateHistorySize",
                value: function _calculateHistorySize() {
                    var calculateItemSize = this.calculateItemSize;
                    var totalSize = 0;
                    this.items.forEach(function(item) {
                        totalSize += calculateItemSize(item);
                    });
                    return totalSize;
                }
            }, {
                key: "undo",
                value: function undo() {
                    if (!this.canUndo()) {
                        return;
                    }
                    this.index--;
                    this.onChange();
                    return this.items[this.index];
                }
            }, {
                key: "redo",
                value: function redo() {
                    if (!this.canRedo()) {
                        return;
                    }
                    this.index++;
                    this.onChange();
                    return this.items[this.index];
                }
            }, {
                key: "canUndo",
                value: function canUndo() {
                    return this.index > 0;
                }
            }, {
                key: "canRedo",
                value: function canRedo() {
                    return this.index < this.items.length - 1;
                }
            }, {
                key: "clear",
                value: function clear() {
                    this.items = [];
                    this.index = -1;
                    this.onChange();
                }
            }]);
            return History;
        }();
        var i18n = __webpack_require__(1);
        var jmespathQuery = __webpack_require__(4);
        var ModeSwitcher = __webpack_require__(9);
        var showSortModal = __webpack_require__(5);
        var showTransformModal = __webpack_require__(6);
        var textmode = __webpack_require__(17);
        var util = __webpack_require__(0);
        var previewmode_textmode = textmode["textModeMixins"][0].mixin;
        var previewmode = {};
        previewmode.create = function(container) {
            var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
            if (typeof options.statusBar === 'undefined') {
                options.statusBar = true;
            }
            options.mainMenuBar = options.mainMenuBar !== false;
            options.enableSort = options.enableSort !== false;
            options.enableTransform = options.enableTransform !== false;
            options.createQuery = options.createQuery || jmespathQuery["a"];
            options.executeQuery = options.executeQuery || jmespathQuery["b"];
            this.options = options;
            if (typeof options.indentation === 'number') {
                this.indentation = Number(options.indentation);
            } else {
                this.indentation = 2;
            }
            Object(i18n["b"])(this.options.languages);
            Object(i18n["a"])(this.options.language);
            this.mode = 'preview';
            var me = this;
            this.container = container;
            this.dom = {};
            this.json = undefined;
            this.text = '';
            this._debouncedValidate = Object(util["debounce"])(this.validate.bind(this), this.DEBOUNCE_INTERVAL);
            this.width = container.clientWidth;
            this.height = container.clientHeight;
            this.frame = document.createElement('div');
            this.frame.className = 'jsoneditor jsoneditor-mode-preview';
            this.frame.onclick = function(event) {
                event.preventDefault();
            }
            ;
            var focusTrackerConfig = {
                target: this.frame,
                onFocus: this.options.onFocus || null,
                onBlur: this.options.onBlur || null
            };
            this.frameFocusTracker = new FocusTracker["a"](focusTrackerConfig);
            this.content = document.createElement('div');
            this.content.className = 'jsoneditor-outer';
            this.dom.busy = document.createElement('div');
            this.dom.busy.className = 'jsoneditor-busy';
            this.dom.busyContent = document.createElement('span');
            this.dom.busyContent.textContent = 'busy...';
            this.dom.busy.appendChild(this.dom.busyContent);
            this.content.appendChild(this.dom.busy);
            this.dom.previewContent = document.createElement('pre');
            this.dom.previewContent.className = 'jsoneditor-preview';
            this.dom.previewText = document.createTextNode('');
            this.dom.previewContent.appendChild(this.dom.previewText);
            this.content.appendChild(this.dom.previewContent);
            if (this.options.mainMenuBar) {
                Object(util["addClassName"])(this.content, 'has-main-menu-bar');
                this.menu = document.createElement('div');
                this.menu.className = 'jsoneditor-menu';
                this.frame.appendChild(this.menu);
                var buttonFormat = document.createElement('button');
                buttonFormat.type = 'button';
                buttonFormat.className = 'jsoneditor-format';
                buttonFormat.title = Object(i18n["c"])('formatTitle');
                this.menu.appendChild(buttonFormat);
                buttonFormat.onclick = function handleFormat() {
                    me.executeWithBusyMessage(function() {
                        try {
                            me.format();
                        } catch (err) {
                            me._onError(err);
                        }
                    }, 'formatting...');
                }
                ;
                var buttonCompact = document.createElement('button');
                buttonCompact.type = 'button';
                buttonCompact.className = 'jsoneditor-compact';
                buttonCompact.title = Object(i18n["c"])('compactTitle');
                this.menu.appendChild(buttonCompact);
                buttonCompact.onclick = function handleCompact() {
                    me.executeWithBusyMessage(function() {
                        try {
                            me.compact();
                        } catch (err) {
                            me._onError(err);
                        }
                    }, 'compacting...');
                }
                ;
                if (this.options.enableSort) {
                    var _sort = document.createElement('button');
                    _sort.type = 'button';
                    _sort.className = 'jsoneditor-sort';
                    _sort.title = Object(i18n["c"])('sortTitleShort');
                    _sort.onclick = function() {
                        me._showSortModal();
                    }
                    ;
                    this.menu.appendChild(_sort);
                }
                if (this.options.enableTransform) {
                    var transform = document.createElement('button');
                    transform.type = 'button';
                    transform.title = Object(i18n["c"])('transformTitleShort');
                    transform.className = 'jsoneditor-transform';
                    transform.onclick = function() {
                        me._showTransformModal();
                    }
                    ;
                    this.dom.transform = transform;
                    this.menu.appendChild(transform);
                }
                var buttonRepair = document.createElement('button');
                buttonRepair.type = 'button';
                buttonRepair.className = 'jsoneditor-repair';
                buttonRepair.title = Object(i18n["c"])('repairTitle');
                this.menu.appendChild(buttonRepair);
                buttonRepair.onclick = function() {
                    if (me.json === undefined) {
                        me.executeWithBusyMessage(function() {
                            try {
                                me.repair();
                            } catch (err) {
                                me._onError(err);
                            }
                        }, 'repairing...');
                    }
                }
                ;
                if (this.options.history !== false) {
                    var onHistoryChange = function onHistoryChange() {
                        me.dom.undo.disabled = !me.history.canUndo();
                        me.dom.redo.disabled = !me.history.canRedo();
                    };
                    var calculateItemSize = function calculateItemSize(item) {
                        return (item.text.length * 2);
                    };
                    this.history = new History(onHistoryChange,calculateItemSize,constants["c"]);
                    var undo = document.createElement('button');
                    undo.type = 'button';
                    undo.className = 'jsoneditor-undo jsoneditor-separator';
                    undo.title = Object(i18n["c"])('undo');
                    undo.onclick = function() {
                        var action = me.history.undo();
                        if (action) {
                            me._applyHistory(action);
                        }
                    }
                    ;
                    this.menu.appendChild(undo);
                    this.dom.undo = undo;
                    var redo = document.createElement('button');
                    redo.type = 'button';
                    redo.className = 'jsoneditor-redo';
                    redo.title = Object(i18n["c"])('redo');
                    redo.onclick = function() {
                        var action = me.history.redo();
                        if (action) {
                            me._applyHistory(action);
                        }
                    }
                    ;
                    this.menu.appendChild(redo);
                    this.dom.redo = redo;
                    this.history.onChange();
                }
                if (this.options && this.options.modes && this.options.modes.length) {
                    this.modeSwitcher = new ModeSwitcher["a"](this.menu,this.options.modes,this.options.mode,function onSwitch(mode) {
                        me.setMode(mode);
                        me.modeSwitcher.focus();
                    }
                    );
                }
            }
            this.errorTable = new ErrorTable["a"]({
                errorTableVisible: true,
                onToggleVisibility: function onToggleVisibility() {
                    me.validate();
                },
                onFocusLine: null,
                onChangeHeight: function onChangeHeight(height) {
                    var statusBarHeight = me.dom.statusBar ? me.dom.statusBar.clientHeight : 0;
                    var totalHeight = height + statusBarHeight + 1;
                    me.content.style.marginBottom = -totalHeight + 'px';
                    me.content.style.paddingBottom = totalHeight + 'px';
                }
            });
            this.frame.appendChild(this.content);
            this.frame.appendChild(this.errorTable.getErrorTable());
            this.container.appendChild(this.frame);
            if (options.statusBar) {
                Object(util["addClassName"])(this.content, 'has-status-bar');
                var statusBar = document.createElement('div');
                this.dom.statusBar = statusBar;
                statusBar.className = 'jsoneditor-statusbar';
                this.frame.appendChild(statusBar);
                this.dom.fileSizeInfo = document.createElement('span');
                this.dom.fileSizeInfo.className = 'jsoneditor-size-info';
                this.dom.fileSizeInfo.innerText = '';
                statusBar.appendChild(this.dom.fileSizeInfo);
                this.dom.arrayInfo = document.createElement('span');
                this.dom.arrayInfo.className = 'jsoneditor-size-info';
                this.dom.arrayInfo.innerText = '';
                statusBar.appendChild(this.dom.arrayInfo);
                statusBar.appendChild(this.errorTable.getErrorCounter());
                statusBar.appendChild(this.errorTable.getWarningIcon());
                statusBar.appendChild(this.errorTable.getErrorIcon());
            }
            this._renderPreview();
            this.setSchema(this.options.schema, this.options.schemaRefs);
        }
        ;
        previewmode._renderPreview = function() {
            var text = this.getText();
            this.dom.previewText.nodeValue = Object(util["limitCharacters"])(text, constants["b"]);
            if (this.dom.fileSizeInfo) {
                this.dom.fileSizeInfo.innerText = 'Size: ' + Object(util["formatSize"])(text.length);
            }
            if (this.dom.arrayInfo) {
                if (Array.isArray(this.json)) {
                    this.dom.arrayInfo.innerText = 'Array: ' + this.json.length + ' items';
                } else {
                    this.dom.arrayInfo.innerText = '';
                }
            }
        }
        ;
        previewmode._onChange = function() {
            this._debouncedValidate();
            if (this.options.onChange) {
                try {
                    this.options.onChange();
                } catch (err) {
                    console.error('Error in onChange callback: ', err);
                }
            }
            if (this.options.onChangeJSON) {
                try {
                    this.options.onChangeJSON(this.get());
                } catch (err) {
                    console.error('Error in onChangeJSON callback: ', err);
                }
            }
            if (this.options.onChangeText) {
                try {
                    this.options.onChangeText(this.getText());
                } catch (err) {
                    console.error('Error in onChangeText callback: ', err);
                }
            }
        }
        ;
        previewmode._showSortModal = function() {
            var me = this;
            function onSort(json, sortedBy) {
                if (Array.isArray(json)) {
                    var sortedArray = Object(util["sort"])(json, sortedBy.path, sortedBy.direction);
                    me.sortedBy = sortedBy;
                    me._setAndFireOnChange(sortedArray);
                }
                if (Object(util["isObject"])(json)) {
                    var sortedObject = Object(util["sortObjectKeys"])(json, sortedBy.direction);
                    me.sortedBy = sortedBy;
                    me._setAndFireOnChange(sortedObject);
                }
            }
            this.executeWithBusyMessage(function() {
                var container = me.options.modalAnchor || constants["a"];
                var json = me.get();
                me._renderPreview();
                Object(showSortModal["showSortModal"])(container, json, function(sortedBy) {
                    me.executeWithBusyMessage(function() {
                        onSort(json, sortedBy);
                    }, 'sorting...');
                }, me.sortedBy);
            }, 'parsing...');
        }
        ;
        previewmode._showTransformModal = function() {
            var _this = this;
            this.executeWithBusyMessage(function() {
                var _this$options = _this.options
                  , createQuery = _this$options.createQuery
                  , executeQuery = _this$options.executeQuery
                  , modalAnchor = _this$options.modalAnchor
                  , queryDescription = _this$options.queryDescription;
                var json = _this.get();
                _this._renderPreview();
                Object(showTransformModal["showTransformModal"])({
                    container: modalAnchor || constants["a"],
                    json: json,
                    queryDescription: queryDescription,
                    createQuery: createQuery,
                    executeQuery: executeQuery,
                    onTransform: function onTransform(query) {
                        _this.executeWithBusyMessage(function() {
                            var updatedJson = executeQuery(json, query);
                            _this._setAndFireOnChange(updatedJson);
                        }, 'transforming...');
                    }
                });
            }, 'parsing...');
        }
        ;
        previewmode.destroy = function() {
            if (this.frame && this.container && this.frame.parentNode === this.container) {
                this.container.removeChild(this.frame);
            }
            if (this.modeSwitcher) {
                this.modeSwitcher.destroy();
                this.modeSwitcher = null;
            }
            this._debouncedValidate = null;
            if (this.history) {
                this.history.clear();
                this.history = null;
            }
            this.frameFocusTracker.destroy();
        }
        ;
        previewmode.compact = function() {
            var json = this.get();
            var text = JSON.stringify(json);
            this._setTextAndFireOnChange(text, json);
        }
        ;
        previewmode.format = function() {
            var json = this.get();
            var text = JSON.stringify(json, null, this.indentation);
            this._setTextAndFireOnChange(text, json);
        }
        ;
        previewmode.repair = function() {
            var text = this.getText();
            try {
                var repairedText = simpleJsonRepair_min_default()(text);
                this._setTextAndFireOnChange(repairedText);
            } catch (err) {}
        }
        ;
        previewmode.focus = function() {
            this.dom.transform.focus();
        }
        ;
        previewmode.set = function(json) {
            if (this.history) {
                this.history.clear();
            }
            this._set(json);
        }
        ;
        previewmode.update = function(json) {
            this._set(json);
        }
        ;
        previewmode._set = function(json) {
            this.text = undefined;
            this.json = json;
            this._renderPreview();
            this._pushHistory();
            this._debouncedValidate();
        }
        ;
        previewmode._setAndFireOnChange = function(json) {
            this._set(json);
            this._onChange();
        }
        ;
        previewmode.get = function() {
            if (this.json === undefined) {
                var text = this.getText();
                this.json = Object(util["parse"])(text);
            }
            return this.json;
        }
        ;
        previewmode.getText = function() {
            if (this.text === undefined) {
                this.text = JSON.stringify(this.json, null, this.indentation);
                if (this.options.escapeUnicode === true) {
                    this.text = Object(util["escapeUnicodeChars"])(this.text);
                }
            }
            return this.text;
        }
        ;
        previewmode.setText = function(jsonText) {
            if (this.history) {
                this.history.clear();
            }
            this._setText(jsonText);
        }
        ;
        previewmode.updateText = function(jsonText) {
            if (this.getText() === jsonText) {
                return;
            }
            this._setText(jsonText);
        }
        ;
        previewmode._setText = function(jsonText, json) {
            if (this.options.escapeUnicode === true) {
                this.text = Object(util["escapeUnicodeChars"])(jsonText);
            } else {
                this.text = jsonText;
            }
            this.json = json;
            this._renderPreview();
            if (this.json === undefined) {
                var me = this;
                this.executeWithBusyMessage(function() {
                    try {
                        me.json = me.get();
                        me._renderPreview();
                        me._pushHistory();
                    } catch (err) {}
                }, 'parsing...');
            } else {
                this._pushHistory();
            }
            this._debouncedValidate();
        }
        ;
        previewmode._setTextAndFireOnChange = function(jsonText, json) {
            this._setText(jsonText, json);
            this._onChange();
        }
        ;
        previewmode._applyHistory = function(action) {
            this.json = action.json;
            this.text = action.text;
            this._renderPreview();
            this._debouncedValidate();
        }
        ;
        previewmode._pushHistory = function() {
            if (!this.history) {
                return;
            }
            var action = {
                text: this.text,
                json: this.json
            };
            this.history.add(action);
        }
        ;
        previewmode.executeWithBusyMessage = function(fn, message) {
            var size = this.getText().length;
            if (size > constants["d"]) {
                var me = this;
                Object(util["addClassName"])(me.frame, 'busy');
                me.dom.busyContent.innerText = message;
                setTimeout(function() {
                    fn();
                    Object(util["removeClassName"])(me.frame, 'busy');
                    me.dom.busyContent.innerText = '';
                }, 100);
            } else {
                fn();
            }
        }
        ;
        previewmode.validate = previewmode_textmode.validate;
        previewmode._renderErrors = previewmode_textmode._renderErrors;
        var previewModeMixins = [{
            mode: 'preview',
            mixin: previewmode,
            data: 'json'
        }];
    }
    )]);
});
(function(a, b) {
    if (typeof define === "function" && define.amd) {
        define([], b);
    } else {
        if (typeof exports === "object") {
            module.exports = b();
        } else {
            a.X2JS = b();
        }
    }
}(this, function() {
    return function(z) {
        var t = "1.2.0";
        z = z || {};
        i();
        u();
        function i() {
            if (z.escapeMode === undefined) {
                z.escapeMode = true;
            }
            z.attributePrefix = z.attributePrefix || "_";
            z.arrayAccessForm = z.arrayAccessForm || "none";
            z.emptyNodeForm = z.emptyNodeForm || "text";
            if (z.enableToStringFunc === undefined) {
                z.enableToStringFunc = true;
            }
            z.arrayAccessFormPaths = z.arrayAccessFormPaths || [];
            if (z.skipEmptyTextNodesForObj === undefined) {
                z.skipEmptyTextNodesForObj = true;
            }
            if (z.stripWhitespaces === undefined) {
                z.stripWhitespaces = true;
            }
            z.datetimeAccessFormPaths = z.datetimeAccessFormPaths || [];
            if (z.useDoubleQuotes === undefined) {
                z.useDoubleQuotes = false;
            }
            z.xmlElementsFilter = z.xmlElementsFilter || [];
            z.jsonPropertiesFilter = z.jsonPropertiesFilter || [];
            if (z.keepCData === undefined) {
                z.keepCData = false;
            }
        }
        var h = {
            ELEMENT_NODE: 1,
            TEXT_NODE: 3,
            CDATA_SECTION_NODE: 4,
            COMMENT_NODE: 8,
            DOCUMENT_NODE: 9
        };
        function u() {}
        function x(B) {
            var C = B.localName;
            if (C == null) {
                C = B.baseName;
            }
            if (C == null || C == "") {
                C = B.nodeName;
            }
            return C;
        }
        function r(B) {
            return B.prefix;
        }
        function s(B) {
            if (typeof (B) == "string") {
                return B.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&apos;");
            } else {
                return B;
            }
        }
        function k(B) {
            return B.replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, '"').replace(/&apos;/g, "'").replace(/&amp;/g, "&");
        }
        function w(C, F, D, E) {
            var B = 0;
            for (; B < C.length; B++) {
                var G = C[B];
                if (typeof G === "string") {
                    if (G == E) {
                        break;
                    }
                } else {
                    if (G instanceof RegExp) {
                        if (G.test(E)) {
                            break;
                        }
                    } else {
                        if (typeof G === "function") {
                            if (G(F, D, E)) {
                                break;
                            }
                        }
                    }
                }
            }
            return B != C.length;
        }
        function n(D, B, C) {
            switch (z.arrayAccessForm) {
            case "property":
                if (!(D[B]instanceof Array)) {
                    D[B + "_asArray"] = [D[B]];
                } else {
                    D[B + "_asArray"] = D[B];
                }
                break;
            }
            if (!(D[B]instanceof Array) && z.arrayAccessFormPaths.length > 0) {
                if (w(z.arrayAccessFormPaths, D, B, C)) {
                    D[B] = [D[B]];
                }
            }
        }
        function a(G) {
            var E = G.split(/[-T:+Z]/g);
            var F = new Date(E[0],E[1] - 1,E[2]);
            var D = E[5].split(".");
            F.setHours(E[3], E[4], D[0]);
            if (D.length > 1) {
                F.setMilliseconds(D[1]);
            }
            if (E[6] && E[7]) {
                var C = E[6] * 60 + Number(E[7]);
                var B = /\d\d-\d\d:\d\d$/.test(G) ? "-" : "+";
                C = 0 + (B == "-" ? -1 * C : C);
                F.setMinutes(F.getMinutes() - C - F.getTimezoneOffset());
            } else {
                if (G.indexOf("Z", G.length - 1) !== -1) {
                    F = new Date(Date.UTC(F.getFullYear(), F.getMonth(), F.getDate(), F.getHours(), F.getMinutes(), F.getSeconds(), F.getMilliseconds()));
                }
            }
            return F;
        }
        function q(D, B, C) {
            if (z.datetimeAccessFormPaths.length > 0) {
                var E = C.split(".#")[0];
                if (w(z.datetimeAccessFormPaths, D, B, E)) {
                    return a(D);
                } else {
                    return D;
                }
            } else {
                return D;
            }
        }
        function b(E, C, B, D) {
            if (C == h.ELEMENT_NODE && z.xmlElementsFilter.length > 0) {
                return w(z.xmlElementsFilter, E, B, D);
            } else {
                return true;
            }
        }
        function A(D, J) {
            if (D.nodeType == h.DOCUMENT_NODE) {
                var K = new Object;
                var B = D.childNodes;
                for (var L = 0; L < B.length; L++) {
                    var C = B.item(L);
                    if (C.nodeType == h.ELEMENT_NODE) {
                        var I = x(C);
                        K[I] = A(C, I);
                    }
                }
                return K;
            } else {
                if (D.nodeType == h.ELEMENT_NODE) {
                    var K = new Object;
                    K.__cnt = 0;
                    var B = D.childNodes;
                    for (var L = 0; L < B.length; L++) {
                        var C = B.item(L);
                        var I = x(C);
                        if (C.nodeType != h.COMMENT_NODE) {
                            var H = J + "." + I;
                            if (b(K, C.nodeType, I, H)) {
                                K.__cnt++;
                                if (K[I] == null) {
                                    K[I] = A(C, H);
                                    n(K, I, H);
                                } else {
                                    if (K[I] != null) {
                                        if (!(K[I]instanceof Array)) {
                                            K[I] = [K[I]];
                                            n(K, I, H);
                                        }
                                    }
                                    (K[I])[K[I].length] = A(C, H);
                                }
                            }
                        }
                    }
                    for (var E = 0; E < D.attributes.length; E++) {
                        var F = D.attributes.item(E);
                        K.__cnt++;
                        K[z.attributePrefix + F.name] = F.value;
                    }
                    var G = r(D);
                    if (G != null && G != "") {
                        K.__cnt++;
                        K.__prefix = G;
                    }
                    if (K["#text"] != null) {
                        K.__text = K["#text"];
                        if (K.__text instanceof Array) {
                            K.__text = K.__text.join("\n");
                        }
                        if (z.stripWhitespaces) {
                            K.__text = K.__text.trim();
                        }
                        delete K["#text"];
                        if (z.arrayAccessForm == "property") {
                            delete K["#text_asArray"];
                        }
                        K.__text = q(K.__text, I, J + "." + I);
                    }
                    if (K["#cdata-section"] != null) {
                        K.__cdata = K["#cdata-section"];
                        delete K["#cdata-section"];
                        if (z.arrayAccessForm == "property") {
                            delete K["#cdata-section_asArray"];
                        }
                    }
                    if (K.__cnt == 0 && z.emptyNodeForm == "text") {
                        K = "";
                    } else {
                        if (K.__cnt == 1 && K.__text != null) {
                            K = K.__text;
                        } else {
                            if (K.__cnt == 1 && K.__cdata != null && !z.keepCData) {
                                K = K.__cdata;
                            } else {
                                if (K.__cnt > 1 && K.__text != null && z.skipEmptyTextNodesForObj) {
                                    if ((z.stripWhitespaces && K.__text == "") || (K.__text.trim() == "")) {
                                        delete K.__text;
                                    }
                                }
                            }
                        }
                    }
                    delete K.__cnt;
                    if (z.enableToStringFunc && (K.__text != null || K.__cdata != null)) {
                        K.toString = function() {
                            return (this.__text != null ? this.__text : "") + (this.__cdata != null ? this.__cdata : "");
                        }
                        ;
                    }
                    return K;
                } else {
                    if (D.nodeType == h.TEXT_NODE || D.nodeType == h.CDATA_SECTION_NODE) {
                        return D.nodeValue;
                    }
                }
            }
        }
        function o(I, F, H, C) {
            var E = "<" + ((I != null && I.__prefix != null) ? (I.__prefix + ":") : "") + F;
            if (H != null) {
                for (var G = 0; G < H.length; G++) {
                    var D = H[G];
                    var B = I[D];
                    if (z.escapeMode) {
                        B = s(B);
                    }
                    E += " " + D.substr(z.attributePrefix.length) + "=";
                    if (z.useDoubleQuotes) {
                        E += '"' + B + '"';
                    } else {
                        E += "'" + B + "'";
                    }
                }
            }
            if (!C) {
                E += ">";
            } else {
                E += "/>";
            }
            return E;
        }
        function j(C, B) {
            return "</" + (C.__prefix != null ? (C.__prefix + ":") : "") + B + ">";
        }
        function v(C, B) {
            return C.indexOf(B, C.length - B.length) !== -1;
        }
        function y(C, B) {
            if ((z.arrayAccessForm == "property" && v(B.toString(), ("_asArray"))) || B.toString().indexOf(z.attributePrefix) == 0 || B.toString().indexOf("__") == 0 || (C[B]instanceof Function)) {
                return true;
            } else {
                return false;
            }
        }
        function m(D) {
            var C = 0;
            if (D instanceof Object) {
                for (var B in D) {
                    if (y(D, B)) {
                        continue;
                    }
                    C++;
                }
            }
            return C;
        }
        function l(D, B, C) {
            return z.jsonPropertiesFilter.length == 0 || C == "" || w(z.jsonPropertiesFilter, D, B, C);
        }
        function c(D) {
            var C = [];
            if (D instanceof Object) {
                for (var B in D) {
                    if (B.toString().indexOf("__") == -1 && B.toString().indexOf(z.attributePrefix) == 0) {
                        C.push(B);
                    }
                }
            }
            return C;
        }
        function g(C) {
            var B = "";
            if (C.__cdata != null) {
                B += "<![CDATA[" + C.__cdata + "]]>";
            }
            if (C.__text != null) {
                if (z.escapeMode) {
                    B += s(C.__text);
                } else {
                    B += C.__text;
                }
            }
            return B;
        }
        function d(C) {
            var B = "";
            if (C instanceof Object) {
                B += g(C);
            } else {
                if (C != null) {
                    if (z.escapeMode) {
                        B += s(C);
                    } else {
                        B += C;
                    }
                }
            }
            return B;
        }
        function p(C, B) {
            if (C === "") {
                return B;
            } else {
                return C + "." + B;
            }
        }
        function f(D, G, F, E) {
            var B = "";
            if (D.length == 0) {
                B += o(D, G, F, true);
            } else {
                for (var C = 0; C < D.length; C++) {
                    B += o(D[C], G, c(D[C]), false);
                    B += e(D[C], p(E, G));
                    B += j(D[C], G);
                }
            }
            return B;
        }
        function e(I, H) {
            var B = "";
            var F = m(I);
            if (F > 0) {
                for (var E in I) {
                    if (y(I, E) || (H != "" && !l(I, E, p(H, E)))) {
                        continue;
                    }
                    var D = I[E];
                    var G = c(D);
                    if (D == null || D == undefined) {
                        B += o(D, E, G, true);
                    } else {
                        if (D instanceof Object) {
                            if (D instanceof Array) {
                                B += f(D, E, G, H);
                            } else {
                                if (D instanceof Date) {
                                    B += o(D, E, G, false);
                                    B += D.toISOString();
                                    B += j(D, E);
                                } else {
                                    var C = m(D);
                                    if (C > 0 || D.__text != null || D.__cdata != null) {
                                        B += o(D, E, G, false);
                                        B += e(D, p(H, E));
                                        B += j(D, E);
                                    } else {
                                        B += o(D, E, G, true);
                                    }
                                }
                            }
                        } else {
                            B += o(D, E, G, false);
                            B += d(D);
                            B += j(D, E);
                        }
                    }
                }
            }
            B += d(I);
            return B;
        }
        this.parseXmlString = function(D) {
            var F = window.ActiveXObject || "ActiveXObject"in window;
            if (D === undefined) {
                return null;
            }
            var E;
            if (window.DOMParser) {
                var G = new window.DOMParser();
                var B = null;
                if (!F) {
                    try {
                        B = G.parseFromString("INVALID", "text/xml").getElementsByTagName("parsererror")[0].namespaceURI;
                    } catch (C) {
                        B = null;
                    }
                }
                try {
                    E = G.parseFromString(D, "text/xml");
                    if (B != null && E.getElementsByTagNameNS(B, "parsererror").length > 0) {
                        E = null;
                    }
                } catch (C) {
                    E = null;
                }
            } else {
                if (D.indexOf("<?") == 0) {
                    D = D.substr(D.indexOf("?>") + 2);
                }
                E = new ActiveXObject("Microsoft.XMLDOM");
                E.async = "false";
                E.loadXML(D);
            }
            return E;
        }
        ;
        this.asArray = function(B) {
            if (B === undefined || B == null) {
                return [];
            } else {
                if (B instanceof Array) {
                    return B;
                } else {
                    return [B];
                }
            }
        }
        ;
        this.toXmlDateTime = function(B) {
            if (B instanceof Date) {
                return B.toISOString();
            } else {
                if (typeof (B) === "number") {
                    return new Date(B).toISOString();
                } else {
                    return null;
                }
            }
        }
        ;
        this.asDateTime = function(B) {
            if (typeof (B) == "string") {
                return a(B);
            } else {
                return B;
            }
        }
        ;
        this.xml2json = function(B) {
            return A(B);
        }
        ;
        this.xml_str2json = function(B) {
            var C = this.parseXmlString(B);
            if (C != null) {
                return this.xml2json(C);
            } else {
                return null;
            }
        }
        ;
        this.json2xml_str = function(B) {
            return e(B, "");
        }
        ;
        this.json2xml = function(C) {
            var B = this.json2xml_str(C);
            return this.parseXmlString(B);
        }
        ;
        this.getVersion = function() {
            return t;
        }
        ;
    }
    ;
}));
