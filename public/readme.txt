使这个应用可以使用ajax进行交互，在footer.inc.php中添加以下的三行代码即可：
若想这个应用只用PHP实现，不需要Ajax进行交互，只需要在footer.inc.php中删除以下三行代码即可：
<script type="text/javascript" src="assets/js/jquery.js"></script>
<script type="text/javascript" src="assets/js/validate.js"></script>
<script type="text/javascript" src="assets/js/init.js"></script>

一言以蔽之，即便不使用CSS和JavaScript,这个web应用程序凭靠纯PHP也能很好地运行，CSS和JavaScript只是对这个web应用程序的点缀。

先用PHP或JSP开发好核心功能的Web应用程序，后用CSS和JavaScript美化外观和体验。

Adding Progressive Enhancements with jQuery
	Progressive enhancement is a term originally coined by Steven Champeon1 in 2003 to describe a webdevelopment
	technique in which applications are designed to be accessible to any Internet connection
	and browser using semantic HTML and other technologies that are applied in layers (such as CSS files
	and JavaScript markup).
	For an application to follow the principles of progressive enhancement, it must adhere to the
	following guidelines:
			• Basic content is accessible to all browsers using the simplest, most semantic（语义、含义）
			HTML markup possible.
			• All of the basic functionality of the app works in all browsers.
			• The user's preferences are respected; this means that the web app doesn't
			override browser settings (such as window size).
			• Externally linked CSS handles the styling and presentation of the document.(涉及CSS---这点非常重要)
			• Externally linked JavaScript enhances the user experience, but it remains unobtrusive, or non-essential to the application's 		
			  operation.(涉及JavaScript---这点非常重要)
	Your application already meets the first four guidelines (it's not pretty, but the application will work
	with styles disabled). So as long as your JavaScript doesn't create any new functionality that can't be
	accessed with JavaScript disabled, you will have successfully created a progressively enhanced web
	application.

Setting Progressive Enhancement Goals
		Using the principles of progressive enhancement, you'll add the ability to view event information
	without a page refresh in a modal window, a content area that sits on top of existing markup to display
	additional information. Such windows are usually triggered by JavaScript, and they are used on many of
	today's most popular web sites.
	In your calendar application, you'll use a modal window to display event details after a user clicks
	the event title. This will be done without a page refresh using AJAX.

loginName:weiwei
password:nihao123!

