<?php





DEFINE("_JC_","Guest"); 
DEFINE("_JC_GUEST_NAME",        "ゲスト");

// Templates
DEFINE("_JC_TPL_ADDCOMMENT",    "コメントを書く");
DEFINE("_JC_TPL_AUTHOR",        "名前");
DEFINE("_JC_TPL_EMAIL",         "メールアドレス");
DEFINE("_JC_TPL_WEBSITE",       "ホームページ");
DEFINE("_JC_TPL_COMMENT",       "コメント");

DEFINE("_JC_TPL_TITLE",       "件名");
DEFINE("_JC_TPL_WRITTEN_BY",    "作者");


// Warning
DEFINE("_JC_CAPTCHA_MISMATCH",  "パスワードが間違っています");
DEFINE("_JC_INVALID_EMAIL",     "メールアドレスが無効です");
DEFINE("_JC_USERNAME_TAKEN",    "その名前はすでに他の方が登録されています。別の名前をご使用ください。");
DEFINE("_JC_NO_GUEST",          "認証エラーが出ました。まず初めにご登録ください。");
DEFINE("_JC_IP_BLOCKED",        "IPアドレスがブロックされました");
DEFINE("_JC_DOMAIN_BLOCKED",    "ドメインアドレスがブロックされました");
DEFINE("_JC_MESSAGE_NEED_MOD",  "コメントを受領しました。まずサイト管理者が拝見します。");
DEFINE("_JC_MESSAGE_ADDED",     "コメントが書込まれました");

// New in 1.3
DEFINE("_JC_TPL_READMORE",       "もっと読む");
DEFINE("_JC_TPL_COMMENTS",       "コメント");   // plural
DEFINE("_JC_TPL_SEC_CODE",       "表示された文字を半角英字で入力してください");   // plural
DEFINE("_JC_TPL_SUBMIT_COMMENTS",       "コメントを送信");   // plural

// New in 1.4
DEFINE("_JC_EMPTY_USERNAME", "名前を入力してください");
DEFINE("_JC_USERNAME_BLOCKED", "ユーザー名がブロックされました");
DEFINE("_JC_TPL_WRITE_COMMENT",     "コメントを書く");
DEFINE("_JC_TPL_GUEST_MUST_LOGIN",  "コメントするにはまずサイトにログインしてください。まだアカウント登録がお済みでないかたは、登録をお願いいたします。");
DEFINE("_JC_TPL_REPORT_POSTING",   "管理人へ報告する");

DEFINE("_JC_TPL_NO_COMMENT",   "このポストへのコメントはありません...");

// New in 1.5
DEFINE("_JC_TPL_HIDESHOW_FORM",   "コメントフォームを表示する／表示しない");
DEFINE("_JC_TPL_HIDESHOW_AREA",   "コメントを表示する／表示しない");
DEFINE("_JC_TPL_REMEMBER_INFO",   "ログイン情報を保存する");

// New in 1.6
DEFINE("_JC_TPL_TOO_SHORT",   "コメントが短かすぎます");
DEFINE("_JC_TPL_TOO_LONG",   "コメントが長すぎます");
DEFINE("_JC_TPL_SUBSCRIBE",   "コメントがついたらメールで通知する");
DEFINE("_JC_TPL_PAGINATE_NEXT",   "次のコメント");
DEFINE("_JC_TPL_PAGINATE_PREV",   "前のコメント");

// New 1.6.8
DEFINE("_JC_TPL_DUPLICATE",   "二重送信");
DEFINE("_JC_TPL_NOSCRIPT",   "コメントを書くには、JavaScriptを有効にして下さい");

// New 1.7
DEFINE("_JC_TPL_INPUT_LOCKED", "This content has been locked. You can no longer post any comment.");
DEFINE("_JC_TPL_TRACKBACK_URI", "TrackBack URI for this entry");
DEFINE("_JC_TPL_COMMENT_RSS_URI", "RSS feed Comments");

// New 1.9
// Do not modify {INTERVAL} as it is from configuration
DEFINE("_JC_TPL_REPOST_WARNING", "Are you trying to spam? Please allow '{INTERVAL}' seconds between posts.");
DEFINE("_JC_TPL_BIGGER", "bigger");
DEFINE("_JC_TPL_SMALLER", "smaller");
DEFINE("_JC_VOTE_VOTED", "Your vote has been submitted");
DEFINE("_JC_NOTIFY_ADMIN", "Comment reported to site administrator");
DEFINE("_JC_LOW_VOTE","Lowly rated comment");
DEFINE("_JC_SHOW_LOW_VOTE","Show");
DEFINE("_JC_VOTE_UP","Vote up");
DEFINE("_JC_VOTE_DOWN","Vote down");
DEFINE("_JC_REPORT","Report");
DEFINE("_JC_TPL_USERSUBSCRIBE","Subscribe via email (Registered users only)");
DEFINE("_JC_TPL_BOOKMARK","Bookmark");
DEFINE("_JC_TPL_MARKING_FAVORITE","Marking article as your favorite, please wait..");
DEFINE("_JC_TPL_MAILTHIS","Email This");
DEFINE("_JC_TPL_FAVORITE","Set as favorite");
DEFINE("_JC_TPL_ADDED_FAVORITE","This article has been added to your favorite list.");
DEFINE("_JC_TPL_HITS","Hits");
DEFINE("_JC_TPL_WARNING_FAVORITE","This article is already in your favorite list.");
DEFINE("_JC_TPL_LINK_FAVORITE","View favorite entry here.");
DEFINE("_JC_TPL_DISPLAY_VOTES","Votes:");
DEFINE("_JC_TPL_MEMBERS_FAV","We are sorry, for registered members only.");
DEFINE("_JC_TPL_AGREE_TERMS","I have read and agree to the");
DEFINE("_JC_TPL_LINK_TERMS","Terms of Usage.");
DEFINE("_JC_TPL_TERMS_WARNING","Please accept the Terms of Usage.");
DEFINE("_JC_TPL_REPORTS_DUP","You cannot report the same comment more than once");
DEFINE("_JC_TPL_VOTINGS_DUP","You cannot vote the same comment more than once");
DEFINE("_JC_TPL_TB_TITLE","Trackback");
DEFINE("_JC_TPL_DOWN_VOTE","vote down");
DEFINE("_JC_TPL_UP_VOTE","vote up");
DEFINE("_JC_TPL_ABUSE_REPORT","report abuse");
DEFINE("_JC_TPL_GOLAST_PAGE","You can add your comment");
DEFINE("_JC_TPL_GOLINK_LAST","here");

// New 2.2
DEFINE("_JC_TPL_UNPUBLISH_ADM", "Unpublish");
DEFINE("_JC_TPL_EDIT_ADM", "Edit");

// New
DEFINE("_JC_TPL_RESTRICTED_AREA", "The comment section is restricted to members only.");
DEFINE("_JC_TPL_PREVIEW_COMMENTS", "Preview");
DEFINE("_JC_TPL_ERROR_PREVIEW", "Please enter a comment.");
DEFINE("_JC_TPL_HEAD_PREVIEW", "Comment Preview");

// New 3.0 (Email this popup)
DEFINE("_JC_TITLE_SHARE", "Share this article with a friend");
DEFINE("_JC_FRIEND_EMAIL", "Friends Email");
DEFINE("_JC_YOUR_NAME", "Your Name");
DEFINE("_JC_YOUR_EMAIL", "Your Email");
DEFINE("_JC_EMAIL_SUBJECT", "Message Subject");
DEFINE("_JC_SEND_BUTTON", "Send");
DEFINE("_JC_RESET_BUTTON", "Reset");

// New 3.0 (Bookmark this popup)
DEFINE("_JC_TITLE_BOOKMARKS", "Share this");

// New 3.0 (Favorites popup)
DEFINE("_JC_TITLE_FAVORITES", "Set As Favorite");

DEFINE("_JC_RECAPTCHA_MISMATCH","Invalid recaptcha password");

DEFINE("_JC_TPL_CLOSEWIN","Close");

DEFINE("_JC_TPL_CBMAILTITLE","A user commented on user");

DEFINE("_JC_TPL_SENT_MAIL","An email has been sent to");

DEFINE("_JC_TPL_TERMS_TITLE","Terms &amp; Conditions");
?>