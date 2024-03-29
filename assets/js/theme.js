jQuery(document).ready(function($) {
    function fix_sidebar() {
        
    }
    (function(e, t, n) {
        function c() {
            s = t[o];(function() 
            {
                r.each(function() 
                {
                    var t = e(this),
                        n = t.width(),
                        r = t.height(),
                        i = e.data(this, a);
                    if (n !== i.w || r !== i.h) 
                    {
                        t.trigger(u, [i.w = n, i.h = r])
                    }
                });
                c()
            }, i[f])
        }
        var r = e([]),
            i = e.resize = e.extend(e.resize, {}),
            s, o = "setTimeout",
            u = "resize",
            a = u + "-special-event",
            f = "delay",
            l = "throttleWindow";
        i[f] = 250;
        i[l] = true;
        e.event.special[u] = {
            setup: function() {
                if (!i[l] && this[o]) {
                    return false
                }
                var t = e(this);
                r = r.add(t);
                e.data(this, a, {
                    w: t.width(),
                    h: t.height()
                });
                if (r.length === 1) {
                    c()
                }
            },
            teardown: function() {
                if (!i[l] && this[o]) {
                    return false
                }
                var t = e(this);
                r = r.not(t);
                t.removeData(a);
                if (!r.length) {
                    clearTimeout(s)
                }
            },
            add: function(t) {
                function s(t, i, s) {
                    var o = e(this),
                        u = e.data(this, a);
                    u.w = i !== n ? i : o.width();
                    u.h = s !== n ? s : o.height();
                    r.apply(this, arguments)
                }
                if (!i[l] && this[o]) {
                    return false
                }
                var r;
                if (e.isFunction(t)) {
                    r = t;
                    return s
                } else {
                    r = t.handler;
                    t.handler = s
                }
            }
        }
    })(jQuery, this)
});
jQuery(document).ready(function($) {
    var left_side_width = 200;
    $(function() {
        "use strict";

        function e() {
            var e = ($(window).height() + 400) - $("body > .header").height() - ($("body > footer").outerHeight() || 0);
            $(".wrapper").css("min-height", e + "px");
            var t = $(".wrapper").height() + 30;
            if (t > e) $(".left-section, html, body").css("min-height", t + "px");
            else {
                $(".left-section, html, body").css("min-height", e + "px")
            }
        }
        $("[data-toggle='offcanvas']").click(function(e) {
            e.preventDefault();
            if ($(window).width() <= 992) {
                $(".row-offcanvas").toggleClass("active");
                $(".left-section").removeClass("collapse-left");
                $(".right-section").removeClass("strech");
                $(".row-offcanvas").toggleClass("relative")
            } else {
                $(".left-section").toggleClass("collapse-left");
                $(".right-section").toggleClass("strech")
            }
        });
        $(".btn").bind("touchstart", function() {
            $(this).addClass("hover")
        }).bind("touchend", function() {
            $(this).removeClass("hover")
        });
        $("[data-toggle='tooltip']").tooltip();
        $(".sidebar .treeview").tree();
        e();
        $(".wrapper").resize(function() {
            e();
            if (!$("body").hasClass("fixed")) {
                return
            }
            $(".sidebar").slimscroll({
                height: ($(window).height() + 1000) - $(".header").height() + "px",
                color: "rgba(0,0,0,0.2)"
            })
        });
        if (!$("body").hasClass("fixed")) {
                    return
                }
                $(".sidebar").slimscroll({
                    height: ($(window).height() + 1000) - $(".header").height() + "px",
                    color: "rgba(0,0,0,0.2)"
                })
    });
    $(window).load(function() {
        (function() {
            var e, t, n, r, i, s, o, u, a, f, l, c, h, p, d, v, m, g, y, b, w, E, S, x, T, N, C, k, L, A, O, M, _, D, P, H, B, j, F, I, q, R, U, z, W, X, V, $ = [].slice,
                J = {}.hasOwnProperty,
                K = function(e, t) {
                    function n() {
                        this.constructor = e
                    }
                    for (var r in t) J.call(t, r) && (e[r] = t[r]);
                    return n.prototype = t.prototype, e.prototype = new n, e.__super__ = t.prototype, e
                },
                Q = [].indexOf || function(e) {
                    for (var t = 0, n = this.length; n > t; t++)
                        if (t in this && this[t] === e) return t;
                    return -1
                };
            for (b = {
                    catchupTime: 500,
                    initialRate: .03,
                    minTime: 500,
                    ghostTime: 500,
                    maxProgressPerFrame: 10,
                    easeFactor: 1.25,
                    startOnPageLoad: !0,
                    restartOnPushState: !0,
                    restartOnRequestAfter: 500,
                    target: "body",
                    elements: {
                        checkInterval: 100,
                        selectors: ["body"]
                    },
                    eventLag: {
                        minSamples: 10,
                        sampleCount: 3,
                        lagThreshold: 3
                    },
                    ajax: {
                        trackMethods: ["GET"],
                        trackWebSockets: !1
                    }
                }, k = function() {
                    var e;
                    return null != (e = "undefined" != typeof performance && null !== performance ? "function" == typeof performance.now ? performance.now() : void 0 : void 0) ? e : +(new Date)
                }, A = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame, y = window.cancelAnimationFrame || window.mozCancelAnimationFrame, null == A && (A = function(e) {
                    return setTimeout(e, 50)
                }, y = function(e) {
                    return clearTimeout(e)
                }), M = function(e) {
                    var t, n;
                    return t = k(), (n = function() {
                        var r;
                        return r = k() - t, r >= 33 ? (t = k(), e(r, function() {
                            return A(n)
                        })) : setTimeout(n, 33 - r)
                    })()
                }, O = function() {
                    var e, t, n;
                    return n = arguments[0], t = arguments[1], e = 3 <= arguments.length ? $.call(arguments, 2) : [], "function" == typeof n[t] ? n[t].apply(n, e) : n[t]
                }, w = function() {
                    var e, t, n, r, i, s, o;
                    for (t = arguments[0], r = 2 <= arguments.length ? $.call(arguments, 1) : [], s = 0, o = r.length; o > s; s++)
                        if (n = r[s])
                            for (e in n) J.call(n, e) && (i = n[e], null != t[e] && "object" == typeof t[e] && null != i && "object" == typeof i ? w(t[e], i) : t[e] = i);
                    return t
                }, v = function(e) {
                    var t, n, r, i, s;
                    for (n = t = 0, i = 0, s = e.length; s > i; i++) r = e[i], n += Math.abs(r), t++;
                    return n / t
                }, S = function(e, t) {
                    var n, r, i;
                    if (null == e && (e = "options"), null == t && (t = !0), i = document.querySelector("[data-pace-" + e + "]")) {
                        if (n = i.getAttribute("data-pace-" + e), !t) return n;
                        try {
                            return JSON.parse(n)
                        } catch (s) {
                            return r = s, "undefined" != typeof console && null !== console ? console.error("Error parsing inline pace options", r) : void 0
                        }
                    }
                }, o = function() {
                    function e() {}
                    return e.prototype.on = function(e, t, n, r) {
                        var i;
                        return null == r && (r = !1), null == this.bindings && (this.bindings = {}), null == (i = this.bindings)[e] && (i[e] = []), this.bindings[e].push({
                            handler: t,
                            ctx: n,
                            once: r
                        })
                    }, e.prototype.once = function(e, t, n) {
                        return this.on(e, t, n, !0)
                    }, e.prototype.off = function(e, t) {
                        var n, r, i;
                        if (null != (null != (r = this.bindings) ? r[e] : void 0)) {
                            if (null == t) return delete this.bindings[e];
                            for (n = 0, i = []; n < this.bindings[e].length;) this.bindings[e][n].handler === t ? i.push(this.bindings[e].splice(n, 1)) : i.push(n++);
                            return i
                        }
                    }, e.prototype.trigger = function() {
                        var e, t, n, r, i, s, o, u, a;
                        if (n = arguments[0], e = 2 <= arguments.length ? $.call(arguments, 1) : [], null != (o = this.bindings) ? o[n] : void 0) {
                            for (i = 0, a = []; i < this.bindings[n].length;) u = this.bindings[n][i], r = u.handler, t = u.ctx, s = u.once, r.apply(null != t ? t : this, e), s ? a.push(this.bindings[n].splice(i, 1)) : a.push(i++);
                            return a
                        }
                    }, e
                }(), null == window.Pace && (window.Pace = {}), w(Pace, o.prototype), L = Pace.options = w({}, b, window.paceOptions, S()), W = ["ajax", "document", "eventLag", "elements"], q = 0, U = W.length; U > q; q++) P = W[q], L[P] === !0 && (L[P] = b[P]);
            a = function(e) {
                function t() {
                    return X = t.__super__.constructor.apply(this, arguments)
                }
                return K(t, e), t
            }(Error), t = function() {
                function e() {
                    this.progress = 0
                }
                return e.prototype.getElement = function() {
                    var e;
                    if (null == this.el) {
                        if (e = document.querySelector(L.target), !e) throw new a;
                        this.el = document.createElement("div"), this.el.className = "pace pace-active", document.body.className = document.body.className.replace("pace-done", ""), document.body.className += " pace-running", this.el.innerHTML = '<div class="pace-progress">\n  <div class="pace-progress-inner"></div>\n</div>\n<div class="pace-activity"></div>', null != e.firstChild ? e.insertBefore(this.el, e.firstChild) : e.appendChild(this.el)
                    }
                    return this.el
                }, e.prototype.finish = function() {
                    var e;
                    return e = this.getElement(), e.className = e.className.replace("pace-active", ""), e.className += " pace-inactive", document.body.className = document.body.className.replace("pace-running", ""), document.body.className += " pace-done"
                }, e.prototype.update = function(e) {
                    return this.progress = e, this.render()
                }, e.prototype.destroy = function() {
                    try {
                        this.getElement().parentNode.removeChild(this.getElement())
                    } catch (e) {
                        a = e
                    }
                    return this.el = void 0
                }, e.prototype.render = function() {
                    var e, t;
                    return null == document.querySelector(L.target) ? !1 : (e = this.getElement(), e.children[0].style.width = "" + this.progress + "%", (!this.lastRenderedProgress || this.lastRenderedProgress | 0 !== this.progress | 0) && (e.children[0].setAttribute("data-progress-text", "" + (0 | this.progress) + "%"), this.progress >= 100 ? t = "99" : (t = this.progress < 10 ? "0" : "", t += 0 | this.progress), e.children[0].setAttribute("data-progress", "" + t)), this.lastRenderedProgress = this.progress)
                }, e.prototype.done = function() {
                    return this.progress >= 100
                }, e
            }(), u = function() {
                function e() {
                    this.bindings = {}
                }
                return e.prototype.trigger = function(e, t) {
                    var n, r, i, s, o;
                    if (null != this.bindings[e]) {
                        for (s = this.bindings[e], o = [], r = 0, i = s.length; i > r; r++) n = s[r], o.push(n.call(this, t));
                        return o
                    }
                }, e.prototype.on = function(e, t) {
                    var n;
                    return null == (n = this.bindings)[e] && (n[e] = []), this.bindings[e].push(t)
                }, e
            }(), I = window.XMLHttpRequest, F = window.XDomainRequest, j = window.WebSocket, E = function(e, t) {
                var n, r, i, s;
                s = [];
                for (r in t.prototype) try {
                    i = t.prototype[r], null == e[r] && "function" != typeof i ? s.push(e[r] = i) : s.push(void 0)
                } catch (o) {
                    n = o
                }
                return s
            }, N = [], Pace.ignore = function() {
                var e, t, n;
                return t = arguments[0], e = 2 <= arguments.length ? $.call(arguments, 1) : [], N.unshift("ignore"), n = t.apply(null, e), N.shift(), n
            }, Pace.track = function() {
                var e, t, n;
                return t = arguments[0], e = 2 <= arguments.length ? $.call(arguments, 1) : [], N.unshift("track"), n = t.apply(null, e), N.shift(), n
            }, D = function(e) {
                var t;
                if (null == e && (e = "GET"), "track" === N[0]) return "force";
                if (!N.length && L.ajax) {
                    if ("socket" === e && L.ajax.trackWebSockets) return !0;
                    if (t = e.toUpperCase(), Q.call(L.ajax.trackMethods, t) >= 0) return !0
                }
                return !1
            }, f = function(e) {
                function t() {
                    var e, n = this;
                    t.__super__.constructor.apply(this, arguments), e = function(e) {
                        var t;
                        return t = e.open, e.open = function(r, i) {
                            return D(r) && n.trigger("request", {
                                type: r,
                                url: i,
                                request: e
                            }), t.apply(e, arguments)
                        }
                    }, window.XMLHttpRequest = function(t) {
                        var n;
                        return n = new I(t), e(n), n
                    }, E(window.XMLHttpRequest, I), null != F && (window.XDomainRequest = function() {
                        var t;
                        return t = new F, e(t), t
                    }, E(window.XDomainRequest, F)), null != j && L.ajax.trackWebSockets && (window.WebSocket = function(e, t) {
                        var r;
                        return r = new j(e, t), D("socket") && n.trigger("request", {
                            type: "socket",
                            url: e,
                            protocols: t,
                            request: r
                        }), r
                    }, E(window.WebSocket, j))
                }
                return K(t, e), t
            }(u), R = null, x = function() {
                return null == R && (R = new f), R
            }, x().on("request", function(t) {
                var n, r, i, s;
                return s = t.type, i = t.request, Pace.running || L.restartOnRequestAfter === !1 && "force" !== D(s) ? void 0 : (r = arguments, n = L.restartOnRequestAfter || 0, "boolean" == typeof n && (n = 0), setTimeout(function() {
                    var t, n, o, u, a, f;
                    if (t = "socket" === s ? i.readyState < 2 : 0 < (u = i.readyState) && 4 > u) {
                        for (Pace.restart(), a = Pace.sources, f = [], n = 0, o = a.length; o > n; n++) {
                            if (P = a[n], P instanceof e) {
                                P.watch.apply(P, r);
                                break
                            }
                            f.push(void 0)
                        }
                        return f
                    }
                }, n))
            }), e = function() {
                function e() {
                    var e = this;
                    this.elements = [], x().on("request", function() {
                        return e.watch.apply(e, arguments)
                    })
                }
                return e.prototype.watch = function(e) {
                    var t, n, r;
                    return r = e.type, t = e.request, n = "socket" === r ? new h(t) : new p(t), this.elements.push(n)
                }, e
            }(), p = function() {
                function e(e) {
                    var t, n, r, i, s, o, u = this;
                    if (this.progress = 0, null != window.ProgressEvent)
                        for (n = null, e.addEventListener("progress", function(e) {
                                return u.progress = e.lengthComputable ? 100 * e.loaded / e.total : u.progress + (100 - u.progress) / 2
                            }), o = ["load", "abort", "timeout", "error"], r = 0, i = o.length; i > r; r++) t = o[r], e.addEventListener(t, function() {
                            return u.progress = 100
                        });
                    else s = e.onreadystatechange, e.onreadystatechange = function() {
                        var t;
                        return 0 === (t = e.readyState) || 4 === t ? u.progress = 100 : 3 === e.readyState && (u.progress = 50), "function" == typeof s ? s.apply(null, arguments) : void 0
                    }
                }
                return e
            }(), h = function() {
                function e(e) {
                    var t, n, r, i, s = this;
                    for (this.progress = 0, i = ["error", "open"], n = 0, r = i.length; r > n; n++) t = i[n], e.addEventListener(t, function() {
                        return s.progress = 100
                    })
                }
                return e
            }(), r = function() {
                function e(e) {
                    var t, n, r, s;
                    for (null == e && (e = {}), this.elements = [], null == e.selectors && (e.selectors = []), s = e.selectors, n = 0, r = s.length; r > n; n++) t = s[n], this.elements.push(new i(t))
                }
                return e
            }(), i = function() {
                function e(e) {
                    this.selector = e, this.progress = 0, this.check()
                }
                return e.prototype.check = function() {
                    var e = this;
                    return document.querySelector(this.selector) ? this.done() : setTimeout(function() {
                        return e.check()
                    }, L.elements.checkInterval)
                }, e.prototype.done = function() {
                    return this.progress = 100
                }, e
            }(), n = function() {
                function e() {
                    var e, t, n = this;
                    this.progress = null != (t = this.states[document.readyState]) ? t : 100, e = document.onreadystatechange, document.onreadystatechange = function() {
                        return null != n.states[document.readyState] && (n.progress = n.states[document.readyState]), "function" == typeof e ? e.apply(null, arguments) : void 0
                    }
                }
                return e.prototype.states = {
                    loading: 0,
                    interactive: 50,
                    complete: 100
                }, e
            }(), s = function() {
                function e() {
                    var e, t, n, r, i, s = this;
                    this.progress = 0, e = 0, i = [], r = 0, n = k(), t = setInterval(function() {
                        var o;
                        return o = k() - n - 50, n = k(), i.push(o), i.length > L.eventLag.sampleCount && i.shift(), e = v(i), ++r >= L.eventLag.minSamples && e < L.eventLag.lagThreshold ? (s.progress = 100, clearInterval(t)) : s.progress = 100 * (3 / (e + 3))
                    }, 50)
                }
                return e
            }(), c = function() {
                function e(e) {
                    this.source = e, this.last = this.sinceLastUpdate = 0, this.rate = L.initialRate, this.catchup = 0, this.progress = this.lastProgress = 0, null != this.source && (this.progress = O(this.source, "progress"))
                }
                return e.prototype.tick = function(e, t) {
                    var n;
                    return null == t && (t = O(this.source, "progress")), t >= 100 && (this.done = !0), t === this.last ? this.sinceLastUpdate += e : (this.sinceLastUpdate && (this.rate = (t - this.last) / this.sinceLastUpdate), this.catchup = (t - this.progress) / L.catchupTime, this.sinceLastUpdate = 0, this.last = t), t > this.progress && (this.progress += this.catchup * e), n = 1 - Math.pow(this.progress / 100, L.easeFactor), this.progress += n * this.rate * e, this.progress = Math.min(this.lastProgress + L.maxProgressPerFrame, this.progress), this.progress = Math.max(0, this.progress), this.progress = Math.min(100, this.progress), this.lastProgress = this.progress, this.progress
                }, e
            }(), H = null, _ = null, m = null, B = null, d = null, g = null, Pace.running = !1, T = function() {
                return L.restartOnPushState ? Pace.restart() : void 0
            }, null != window.history.pushState && (z = window.history.pushState, window.history.pushState = function() {
                return T(), z.apply(window.history, arguments)
            }), null != window.history.replaceState && (V = window.history.replaceState, window.history.replaceState = function() {
                return T(), V.apply(window.history, arguments)
            }), l = {
                ajax: e,
                elements: r,
                document: n,
                eventLag: s
            }, (C = function() {
                var e, n, r, i, s, o, u, a;
                for (Pace.sources = H = [], o = ["ajax", "elements", "document", "eventLag"], n = 0, i = o.length; i > n; n++) e = o[n], L[e] !== !1 && H.push(new l[e](L[e]));
                for (a = null != (u = L.extraSources) ? u : [], r = 0, s = a.length; s > r; r++) P = a[r], H.push(new P(L));
                return Pace.bar = m = new t, _ = [], B = new c
            })(), Pace.stop = function() {
                return Pace.trigger("stop"), Pace.running = !1, m.destroy(), g = !0, null != d && ("function" == typeof y && y(d), d = null), C()
            }, Pace.restart = function() {
                return Pace.trigger("restart"), Pace.stop(), Pace.start()
            }, Pace.go = function() {
                return Pace.running = !0, m.render(), g = !1, d = M(function(e, t) {
                    var n, r, i, s, o, u, a, f, l, h, p, d, v, y, b, w, E;
                    for (f = 100 - m.progress, r = d = 0, i = !0, u = v = 0, b = H.length; b > v; u = ++v)
                        for (P = H[u], h = null != _[u] ? _[u] : _[u] = [], o = null != (E = P.elements) ? E : [P], a = y = 0, w = o.length; w > y; a = ++y) s = o[a], l = null != h[a] ? h[a] : h[a] = new c(s), i &= l.done, l.done || (r++, d += l.tick(e));
                    return n = d / r, m.update(B.tick(e, n)), p = k(), m.done() || i || g ? (m.update(100), Pace.trigger("done"), setTimeout(function() {
                        return m.finish(), Pace.running = !1, Pace.trigger("hide")
                    }, Math.max(L.ghostTime, Math.min(L.minTime, k() - p)))) : t()
                })
            }, Pace.start = function(e) {
                w(L, e), Pace.running = !0;
                try {
                    m.render()
                } catch (t) {
                    a = t
                }
                return document.querySelector(".pace") ? (Pace.trigger("start"), Pace.go()) : setTimeout(Pace.start, 50)
            }, "function" == typeof define && define.amd ? define("theme-app", [], function() {
                return Pace
            }) : "object" == typeof exports ? module.exports = Pace : L.startOnPageLoad && Pace.start()
        }).call(this)
    })
});
(function(e) {
    "use strict";
    e.fn.tree = function() {
        return this.each(function() {
            var t = e(this).children("a").first();
            var n = e(this).children(".treeview-menu").first();
            var r = e(this).hasClass("active");
            if (r) {
                n.show();
                t.children(".fa-angle-left").first().removeClass("fa-angle-left").addClass("ion-arrow-down-b")
            }
            t.click(function(e) {
                e.preventDefault();
                if (r) {
                    n.slideUp();
                    r = false;
                    t.children(".ion-arrow-down-b").first().removeClass("ion-arrow-down-b").addClass("fa-angle-left");
                    t.parent("li").removeClass("active")
                } else {
                    n.slideDown();
                    r = true;
                    t.children(".fa-angle-left").first().removeClass("fa-angle-left").addClass("ion-arrow-down-b");
                    t.parent("li").addClass("active")
                }
            });
            n.find("li > a").each(function() {
                var t = parseInt(e(this).css("margin-left")) + 10;
                e(this).css({
                    "margin-left": t + "px"
                })
            })
        })
    }
})(jQuery)