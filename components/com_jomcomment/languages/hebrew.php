<?php





/**
*
* @version $Id: hebrew.php,v 1.0.0 2006/09/09 14:47 MtK Exp $
* @package JomComment
* @subpackage languages
* @copyright Copyright (C) 2006-2007 Mati Kochen. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* 
* JomCooment - http://www.azrul.com
* Mati Kochen - mtk@012.net.il
*
*/

DEFINE("_JC_","אורח");
DEFINE("_JC_GUEST_NAME",        "אורח");

// Templates
DEFINE("_JC_TPL_ADDCOMMENT",    "הוסף תגובה");
DEFINE("_JC_TPL_AUTHOR",        "שם");
DEFINE("_JC_TPL_EMAIL",         "דוא\"ל");
DEFINE("_JC_TPL_WEBSITE",       "אתר");
DEFINE("_JC_TPL_COMMENT",       "תגובה");

DEFINE("_JC_TPL_TITLE",       	"כותרת");
DEFINE("_JC_TPL_WRITTEN_BY",    "נכתב ע\"י");


// Warning
DEFINE("_JC_CAPTCHA_MISMATCH",  "סיסמא שגויה");
DEFINE("_JC_INVALID_EMAIL",     "כתובת דוא\"ל שגויה");
DEFINE("_JC_USERNAME_TAKEN",    "שם זה רשום כבר. אנא נסה שם אחר");
DEFINE("_JC_NO_GUEST",          "שגיאת גישה. אנא הרשם");
DEFINE("_JC_IP_BLOCKED",        "כתובת ה-IP שלך חסומה");
DEFINE("_JC_DOMAIN_BLOCKED",    "כתובת הדומיין שלך חסומה");
DEFINE("_JC_MESSAGE_NEED_MOD",  "תגובתך נוספה. היא תאושר ע\"י מנהל המערכת");
DEFINE("_JC_MESSAGE_ADDED",     "תגובתך נוספה");

// New in 1.3
DEFINE("_JC_TPL_READMORE",       "קרא עוד");
DEFINE("_JC_TPL_COMMENTS",       "תגובות");   // plural
DEFINE("_JC_TPL_SEC_CODE",       "העתק תווים מוצגים");   // plural
DEFINE("_JC_TPL_SUBMIT_COMMENTS",       "הוסף תגובה");   // plural

// New in 1.4
DEFINE("_JC_EMPTY_USERNAME", "אנא הזן את שמך");
DEFINE("_JC_USERNAME_BLOCKED", "שמך נחסף");
DEFINE("_JC_TPL_WRITE_COMMENT",     "כתוב תגובה");
DEFINE("_JC_TPL_GUEST_MUST_LOGIN",  "הנך חייב להיות מחובר ע\"מ להזין תגובות. אנא הרשם, אם טרם עשית זאת.");
DEFINE("_JC_TPL_REPORT_POSTING",   "דווח למנהל");

DEFINE("_JC_TPL_NO_COMMENT",   "אין תגובות למאמר זה...");

// New in 1.5
DEFINE("_JC_TPL_HIDESHOW_FORM",   "הצג/הסתר טופס תגובה");
DEFINE("_JC_TPL_HIDESHOW_AREA",   "הצג/הסתר תגובות");
DEFINE("_JC_TPL_REMEMBER_INFO",   "זכור מידע?");

// New in 1.6
DEFINE("_JC_TPL_TOO_SHORT",   "תגובתך קצרה מידי");
DEFINE("_JC_TPL_TOO_LONG",   "תגובתך ארוכה מידי");
DEFINE("_JC_TPL_SUBSCRIBE",   "קבל דוא\"ל כאשר תפורסם תגובה");
DEFINE("_JC_TPL_PAGINATE_NEXT",   "תגובה הבאה");
DEFINE("_JC_TPL_PAGINATE_PREV",   "תגובה קודמת");

// New 1.6.8
DEFINE("_JC_TPL_DUPLICATE",   "Duplicate entry detected");
DEFINE("_JC_TPL_NOSCRIPT",   "Please enable JavaScript to post a new comment");

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