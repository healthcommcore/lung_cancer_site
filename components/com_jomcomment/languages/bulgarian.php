<?php





DEFINE("_JC_","Guest"); 
DEFINE("_JC_GUEST_NAME",        "гост");

// Templates
DEFINE("_JC_TPL_ADDCOMMENT",    "Добави коментар");
DEFINE("_JC_TPL_AUTHOR",        "Име");
DEFINE("_JC_TPL_EMAIL",         "Email адрес");
DEFINE("_JC_TPL_WEBSITE",       "Уеб сайт");
DEFINE("_JC_TPL_COMMENT",       "Коментар");

DEFINE("_JC_TPL_TITLE",       "Заглавие");
DEFINE("_JC_TPL_WRITTEN_BY",    "написан от");


// Warning
DEFINE("_JC_CAPTCHA_MISMATCH",  "Невалиден код за сигурност");
DEFINE("_JC_INVALID_EMAIL",     "Невалиден email адрес");
DEFINE("_JC_USERNAME_TAKEN",    "Името е регистрирано, моля опитайте с друго.");
DEFINE("_JC_NO_GUEST",          "Нямата нужните права. Моля регистрирайте се първо");
DEFINE("_JC_IP_BLOCKED",        "Вашият IP адрес е блокиран");
DEFINE("_JC_DOMAIN_BLOCKED",    "Вашият домейн адрес е блокиран");
DEFINE("_JC_MESSAGE_NEED_MOD",  "Коментарът е изпратен до администратор за одобрение");
DEFINE("_JC_MESSAGE_ADDED",     "Коментарът е публикуван");

// New in 1.3
DEFINE("_JC_TPL_READMORE",       "Продължава");
DEFINE("_JC_TPL_COMMENTS",       "Коментара");   // plural
DEFINE("_JC_TPL_SEC_CODE",       "Моля въведете показаните символи");   // plural
DEFINE("_JC_TPL_SUBMIT_COMMENTS",       "Добави коментар");   // plural

// New in 1.4
DEFINE("_JC_EMPTY_USERNAME", "Моля въведете вашето име");
DEFINE("_JC_USERNAME_BLOCKED", "Вашето потребителско име е блокирано");
DEFINE("_JC_TPL_WRITE_COMMENT",     "Напиши коментар");
DEFINE("_JC_TPL_GUEST_MUST_LOGIN",  "Трябва да сте влезли в сайта, за да коментирате. Моля регистрирайте се, ако все още нямате акаунт.");
DEFINE("_JC_TPL_REPORT_POSTING",   "Информирай администратор");

DEFINE("_JC_TPL_NO_COMMENT",   "Все още няма коментари по тази тема");

// New in 1.5
DEFINE("_JC_TPL_HIDESHOW_FORM",   "Скрий/покажи формата за коментар");
DEFINE("_JC_TPL_HIDESHOW_AREA",   "Скрий/покажи коментарите");
DEFINE("_JC_TPL_REMEMBER_INFO",   "Remember info?");

// New in 1.6
DEFINE("_JC_TPL_TOO_SHORT",   "Вашият коментар е твърде кратък");
DEFINE("_JC_TPL_TOO_LONG",   "Вашият коментар е твърде дълъг");
DEFINE("_JC_TPL_SUBSCRIBE",   "Извести с Email при публикуване на нов коментар");
DEFINE("_JC_TPL_PAGINATE_NEXT",   "следващи коментари");
DEFINE("_JC_TPL_PAGINATE_PREV",   "предишни коментари");

// New 1.6.8
DEFINE("_JC_TPL_DUPLICATE",   "Двоен коментар");
DEFINE("_JC_TPL_NOSCRIPT",   "Моля активирайте JavaScript за да публикувате коментар");

// New 1.7
DEFINE("_JC_TPL_INPUT_LOCKED", "Тази тема е заключена. Не може да пуснете коментар.");
DEFINE("_JC_TPL_TRACKBACK_URI", "TrackBack URI за тази тема");
DEFINE("_JC_TPL_COMMENT_RSS_URI", "Следете за нови коментари с RSS");

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