(function() {
    "use strict";
    var reply_username = [];
    $('tbody strong a.dark').each(function(idx, element) {
        var $element = $(element);
        var username = $element.attr('href');
        username = username.substring(8);
        reply_username.push(username);
    });
    var arrlen = reply_username.length;

    function autocomplete(input, keycode, reply_username) {
        if (keycode == 37) {
            return false;
        }
        var $this = input;
        var old_content = $this.val();
        var current_user_focus_index = $this.getCursorPosition();
        current_user_focus_index--;
        var current_input_username_index = old_content.lastIndexOf('@', current_user_focus_index);
        if (current_input_username_index == -1) {
            return false;
        }
        var input_username = old_content.substring((current_input_username_index + 1));
        if (input_username.indexOf(' ') != -1) {
            var current_substr_index = old_content.indexOf(' ', (current_input_username_index + 1));
            if (current_substr_index <= current_user_focus_index) {
                return false;
            }
            input_username = old_content.substring((current_input_username_index + 1), current_substr_index);
        }
        if (input_username.indexOf('@') != -1) {
            var current_substr_index = old_content.indexOf('@', (current_input_username_index + 1));
            input_username = old_content.substring((current_input_username_index + 1), current_substr_index);
        }
        if (input_username == '') {
            return false;
        }
        for (var i = 0; i < arrlen; i++) {
            if (reply_username[i].indexOf(input_username) == -1) {
                continue;
            }
            var new_content = old_content.substring(0, current_input_username_index);
            new_content += '@';
            new_content += reply_username[i];
            var endText = new_content.length;
            if (keycode == 46 || keycode == 8) {
                if (endText != (current_user_focus_index + 2) || reply_username[i].length != (input_username.length + 1)) {
                    return false;
                }
                new_content = old_content.substring(0, current_input_username_index);
                endText = new_content.length;
            }
            if (keycode == 39) {
                if (endText != (current_user_focus_index + 1) || reply_username[i] != input_username) {
                    return false;
                }
            }
            if (typeof(current_substr_index) === 'undefined' && keycode != 46 && keycode != 8) {
                new_content += ' ';
            }
            if (typeof(current_substr_index) !== 'undefined') {
                new_content += old_content.substring(current_substr_index);
            }
            $this.val(new_content);
            if (input_username != reply_username[i]) {
                var selectedCompleteText = document.getElementById('reply_content');
                var startText = new_content.lastIndexOf(input_username) + input_username.length;
                setInputSelection(selectedCompleteText, startText, endText);
            }
            if (input_username == reply_username[i]) {
                var selectedCompleteText = document.getElementById('reply_content');
                endText = new_content.length;
                setInputSelection(selectedCompleteText, endText, endText);
            }
            return false;
        }
    }

    function setInputSelection(input, startPos, endPos) {
        input.focus();
        if (typeof input.selectionStart != "undefined") {
            input.selectionStart = startPos;
            input.selectionEnd = endPos;
        } else if (document.selection && document.selection.createRange) {
            // IE branch
            input.select();
            var range = document.selection.createRange();
            range.collapse(true);
            range.moveEnd("character", endPos);
            range.moveStart("character", startPos);
            range.select();
        }
    }
    $.fn.getCursorPosition = function() {
        var el = $(this).get(0);
        var pos = 0;
        if ('selectionStart' in el) {
            pos = el.selectionStart;
        } else if ('selection' in document) {
            el.focus();
            var Sel = document.selection.createRange();
            var SelLength = document.selection.createRange().text.length;
            Sel.moveStart('character', -el.value.length);
            pos = Sel.text.length - SelLength;
        }
        return pos;
    }
    $("#reply_content").keyup(function(e) {
        var keycode = e.which;
        autocomplete($(this), keycode, reply_username);
    });
})(jQuery)
