<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Eloquent ORM Editor</title>
    <style type="text/css">
        body {
            background-color: #f5f5f5;
            font-family: 'Arial', sans-serif;
            width: 98%;
            margin: 0 auto;
        }

        #passcode-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 400px;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: auto; /* Added to center the form horizontally */
            margin-top: 10vh; /* Adjust this value to center vertically */
        }

        label {
            font-size: 18px;
            margin-bottom: 10px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .button {
            background-color: #1c2833;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 2px 2px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #0056b3;
        }

        #logout-button {
            margin-top: 10px;
        }

        #json-input {
            display: block;
            width: 100%;
            height: 200px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            padding: 10px;
            box-sizing: border-box;
            color: #1c2833;
        }

        #json-display {
            border: 1px solid #ddd;
            margin: 0;
            padding: 10px 20px;
            white-space: pre-line;
            font-size: 14px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #sidebar {
            list-style-type: none;
            padding: 0;
        }

        #sidebar li {
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s;
        }

        #sidebar a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #1c2833;
            background-color: #f8f9fa; /* Background color */
            transition: background-color 0.3s;
            font-size: 14px;
        }

        #sidebar-title a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #1c2833;
        }

        #sidebar a:hover {
            background-color: #1c2833; /* Hover background color */
            color: #fff;
        }

    </style>
</head>
<body>
@if(!session('passcode_verified'))
    <br/>
    <form id="passcode-form" method="post" action="{{ route('orm') }}">
        @csrf
        <label for="passcode">PASSCODE: </label><br/>
        <input type="text" name="passcode"/><br/>
        <input type="hidden" name="mode" value="passcode"/>
        <input class="button" type="submit" value="Verify"/>
    </form>
@else
    <div style="display: flex;">

        <!-- Sidebar Section -->
        <div style="flex: 1; padding-right: 20px;">
            <h2 id="sidebar-title"><a href="{{ route("orm") }}">Eloq Editor</a></h2>
            <ul id="sidebar">
                @foreach($models as $model)
                    <li>
                        <a href="{{ route('orm', ['model' => $model]) }}" >{{ $model }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Main Content Section -->
        <div style="flex: 8;">
            <form method="post" action="{{ route('orm') }}">
                @csrf
                <input type="hidden" name="mode" value="logout"/>
                <button id="logout-button" class="button" style="float: right;">Logout</button>
            </form>
            <form id="json-input-form" method="post" action="{{ route('orm') }}">
                @csrf
                <textarea id="json-input" name="orm_query">{{ session('query') }}</textarea>
                <input type="hidden" name="mode" value="editor"/>
                <input class="button" type="submit" value="Submit"/>
            </form>
            <br/>
            <pre id="json-display"></pre>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script>
        !function () {
            var e = '/* Syntax highlighting for JSON objects */ .json-editor-blackbord {   background: #1c2833;   color: #fff;   font-size: 13px;   font-family: Menlo,Monaco,Consolas,"Courier New",monospace; } @media screen and (min-width: 1600px) {   .json-editor-blackbord {     font-size: 14px;   } }  ul.json-dict, ol.json-array {   list-style-type: none;   margin: 0 0 0 1px;   border-left: 1px dotted #525252;   padding-left: 2em; } .json-string {   /*color: #0B7500;*/   /*color: #BCCB86;*/   color: #0ad161; } .json-literal {   /*color: #1A01CC;*/   /*font-weight: bold;*/   color: #ff8c00; } .json-url {   color: #1e90ff; } .json-property {   color: #4fdee5;   line-height: 160%;   font-weight: 500; }  /* Toggle button */ a.json-toggle {   position: relative;   color: inherit;   text-decoration: none;   cursor: pointer; } a.json-toggle:focus {   outline: none; } a.json-toggle:before {   color: #aaa;   content: "\\25BC"; /* down arrow */   position: absolute;   display: inline-block;   width: 1em;   left: -1em; } a.json-toggle.collapsed:before {   transform: rotate(-90deg); /* Use rotated down arrow, prevents right arrow appearing smaller than down arrow in some browsers */   -ms-transform: rotate(-90deg);   -webkit-transform: rotate(-90deg); }   /* Collapsable placeholder links */ a.json-placeholder {   color: #aaa;   padding: 0 1em;   text-decoration: none;   cursor: pointer; } a.json-placeholder:hover {   text-decoration: underline; }',
                o = function (e) {
                    var o = document.getElementsByTagName("head")[0], t = document.createElement("style");
                    if (o.appendChild(t), t.styleSheet) t.styleSheet.disabled || (t.styleSheet.cssText = e); else try {
                        t.innerHTML = e
                    } catch (n) {
                        t.innerText = e
                    }
                };
            o(e)
        }(), function (e) {
            function o(e) {
                return e instanceof Object && Object.keys(e).length > 0
            }

            function t(e) {
                var o = /^(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
                return o.test(e)
            }

            function n(e, r) {
                var s = "";
                if ("string" == typeof e) e = e.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"), s += t(e) ? '<a href="' + e + '" class="json-string json-url">"' + e + '"</a>' : '<span class="json-string">"' + e + '"</span>'; else if ("number" == typeof e) s += '<span class="json-literal json-literal-number">' + e + "</span>"; else if ("boolean" == typeof e) s += '<span class="json-literal json-literal-boolean">' + e + "</span>"; else if (null === e) s += '<span class="json-literal json-literal-null">null</span>'; else if (e instanceof Array) if (e.length > 0) {
                    s += '[<ol class="json-array">';
                    for (var l = 0; l < e.length; ++l) s += "<li>", o(e[l]) && (s += '<a href class="json-toggle"></a>'), s += n(e[l], r), l < e.length - 1 && (s += ","), s += "</li>";
                    s += "</ol>]"
                } else s += "[]"; else if ("object" == typeof e) {
                    var a = Object.keys(e).length;
                    if (a > 0) {
                        s += '{<ul class="json-dict">';
                        for (var i in e) if (e.hasOwnProperty(i)) {
                            s += "<li>";
                            var c = r.withQuotes ? '<span class="json-string json-property">"' + i + '"</span>' : '<span class="json-property">' + i + "</span>";
                            s += o(e[i]) ? '<a href class="json-toggle"></a>' + c : c, s += ": " + n(e[i], r), --a > 0 && (s += ","), s += "</li>"
                        }
                        s += "</ul>}"
                    } else s += "{}"
                }
                return s
            }

            e.fn.jsonViewer = function (t, r) {
                return r = r || {}, this.each(function () {
                    var s = n(t, r);
                    o(t) && (s = '<a href class="json-toggle"></a>' + s), e(this).html(s), e(this).off("click"), e(this).on("click", "a.json-toggle", function () {
                        var o = e(this).toggleClass("collapsed").siblings("ul.json-dict, ol.json-array");
                        if (o.toggle(), o.is(":visible")) o.siblings(".json-placeholder").remove(); else {
                            var t = o.children("li").length, n = t + (t > 1 ? " items" : " item");
                            o.after('<a href class="json-placeholder">' + n + "</a>")
                        }
                        return !1
                    }), e(this).on("click", "a.json-placeholder", function () {
                        return e(this).siblings("a.json-toggle").click(), !1
                    }), 1 == r.collapsed && e(this).find("a.json-toggle").click()
                })
            }
        }(jQuery), function (e) {
            function o(e) {
                var o = {'"': '\\"', "\\": "\\\\", "\b": "\\b", "\f": "\\f", "\n": "\\n", "\r": "\\r", "	": "\\t"};
                return e.replace(/["\\\b\f\n\r\t]/g, function (e) {
                    return o[e]
                })
            }

            function t(e) {
                if ("string" == typeof e) return o(e);
                if ("object" == typeof e) for (var n in e) e[n] = t(e[n]); else if (Array.isArray(e)) for (var r = 0; r < e.length; r++) e[r] = t(e[r]);
                return e
            }

            function n(o, t, n) {
                n = n || {}, n.editable !== !1 && (n.editable = !0), this.$container = e(o), this.options = n, this.load(t)
            }

            n.prototype = {
                constructor: n, load: function (e) {
                    this.$container.jsonViewer(t(e), {
                        collapsed: this.options.defaultCollapsed,
                        withQuotes: !0
                    }).addClass("json-editor-blackbord").attr("contenteditable", !!this.options.editable)
                }, get: function () {
                    try {
                        return this.$container.find(".collapsed").click(), JSON.parse(this.$container.text())
                    } catch (e) {
                        throw new Error(e)
                    }
                }
            }, window.JsonEditor = n
        }(jQuery);
    </script>
    <script type="text/javascript">
        var editor = new JsonEditor('#json-display', JSON.parse('@json($data)'));

        $(document).ready(function () {
            $('#json-input').keydown(function (e) {
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    $('#json-input-form').submit();
                }
            });
        });
    </script>
@endif
</body>
</html>
