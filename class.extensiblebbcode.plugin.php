<?php if (!defined('APPLICATION')) exit();
$PluginInfo['ExtensibleBBCode'] = array(
    'Description' => 'This is basically a straight copy of Vanilla\'s default BBCode capabilities, found in /library/core/class.format.php, but
    				in plugin form, so now you can edit to your heart\'s content without having to worry about maintaining a forked core.',
    'Version' => '1.0.0',
    'RequiredApplications' => array('Vanilla' => '2.1'),
    'RequiredTheme' => FALSE,
    'RequiredPlugins' => FALSE,
    'HasLocale' => FALSE,
    'Author' => "James Ducker",
    'AuthorEmail' => 'james.ducker@gmail.com',
    'AuthorUrl' => 'https://github.com/jamesinc',
	'License' => 'GPL-3.0'
);

// This overrides the default BBCode formatter with this plugin
Gdn::FactoryInstall('BBCodeFormatter', 'ExtensibleBBCodePlugin', __FILE__, Gdn::FactorySingleton);

class ExtensibleBBCodePlugin extends Gdn_Plugin {

	/**
	 * Custom formatting calls. Put your custom preg_replace rules here
	 * @param  string $Mixed Markup that has already gone through format()
	 * @return string        Parsed markup
	 */
	private static function customFormat($Mixed='') {

		// Ordered lists
		$Mixed = preg_replace_callback("#\[list\=1\](.*?)\[/list\]#si", array('ExtensibleBBCodePlugin', 'orderedListCallback'), $Mixed);
		//
		// PUT YOUR CUSTOM BBCODE FORMATTING HERE!
		//

		return $Mixed;

	}

	/**
	 * Generates an ordered list. Largely copied from class.format.php's unordered list impl.
	 * @param  Array $Matches Matches from the initial format regex replace capturing group
	 * @return String          HTML formatted ordered list (<ol>)
	 */
    private static function orderedListCallback($Matches) {
        $Content = explode("[*]", $Matches[1]);
        $Result = '';
        foreach ($Content as $Item) {
            if (trim($Item) != '') {
                $Result .= '<li>'.$Item.'</li>';
            }
        }
        $Result = '<ol>'.$Result.'</ol>';
        return $Result;
    }

    /**
     * Copied from class.format.php
     */
    private static function listCallback($Matches) {
        $Content = explode("[*]", $Matches[1]);
        $Result = '';
        foreach ($Content as $Item) {
            if (trim($Item) != '') {
                $Result .= '<li>'.$Item.'</li>';
            }
        }
        $Result = '<ul>'.$Result.'</ul>';
        return $Result;
    }

	/**
	 * Contains default Vanilla BBCode parsing.
	 * @param  string $Mixed Raw markup to parse
	 * @return string        Parsed markup
	 */
	public static function format($Mixed='') {

        $Mixed = preg_replace("#\[noparse\](.*?)\[/noparse\]#sie", "str_replace(array('[',']',':'), array('&#91;','&#93;','&#58;'), htmlspecialchars('\\1'))", $Mixed);
        $Mixed = str_ireplace(array("[php]", "[mysql]", "[css]"), "[code]", $Mixed);
        $Mixed = str_ireplace(array("[/php]", "[/mysql]", "[/css]"), "[/code]", $Mixed);
        $Mixed = preg_replace("#\[code\](.*?)\[/code\]#sie", "'<div class=\"PreContainer\"><pre>'.str_replace(array('[',']',':'), array('&#91;','&#93;','&#58;'), htmlspecialchars('\\1')).'</pre></div>'", $Mixed);
        $Mixed = preg_replace("#\[b\](.*?)\[/b\]#si", '<b>\\1</b>', $Mixed);
        $Mixed = preg_replace("#\[i\](.*?)\[/i\]#si", '<i>\\1</i>', $Mixed);
        $Mixed = preg_replace("#\[u\](.*?)\[/u\]#si", '<u>\\1</u>', $Mixed);
        $Mixed = preg_replace("#\[s\](.*?)\[/s\]#si", '<s>\\1</s>', $Mixed);
        $Mixed = preg_replace("#\[strike\](.*?)\[/strike\]#si", '<s>\\1</s>', $Mixed);
        $Mixed = preg_replace("#\[quote=[\"']?([^\]]+)(;[\d]+)?[\"']?\](.*?)\[/quote\]#si", '<blockquote class="Quote" rel="\\1"><div class="QuoteAuthor">'.sprintf(T('%s said:'), '\\1').'</div><div class="QuoteText">\\3</div></blockquote>', $Mixed);
        $Mixed = preg_replace("#\[quote\](.*?)\[/quote\]#si", '<blockquote class="Quote"><div class="QuoteText">\\1</div></blockquote>', $Mixed);
        $Mixed = preg_replace("#\[cite\](.*?)\[/cite\]#si", '<blockquote class="Quote">\\1</blockquote>', $Mixed);
        $Mixed = preg_replace("#\[hide\](.*?)\[/hide\]#si", '\\1', $Mixed);
        $Mixed = preg_replace("#\[url\]((https?|ftp):\/\/.*?)\[/url\]#si", '<a rel="nofollow" target="_blank" href="\\1">\\1</a>', $Mixed);
        $Mixed = preg_replace("#\[url\](.*?)\[/url\]#si", '\\1', $Mixed);
        $Mixed = preg_replace("#\[url=[\"']?((https?|ftp):\/\/.*?)[\"']?\](.*?)\[/url\]#si", '<a rel="nofollow" target="_blank" href="\\1">\\3</a>', $Mixed);
        $Mixed = preg_replace("#\[url=[\"']?(.*?)[\"']?\](.*?)\[/url\]#si", '\\2', $Mixed);
        $Mixed = preg_replace("#\[img\]((https?|ftp):\/\/.*?)\[/img\]#si", '<img src="\\1" border="0" />', $Mixed);
        $Mixed = preg_replace("#\[img\](.*?)\[/img\]#si", '\\1', $Mixed);
        $Mixed = preg_replace("#\[img=[\"']?((https?|ftp):\/\/.*?)[\"']?\](.*?)\[/img\]#si", '<img src=\\1" border="0" alt="\\3" />', $Mixed);
        $Mixed = preg_replace("#\[img=[\"']?(.*?)[\"']?\](.*?)\[/img\]#si", '\\2', $Mixed);
        $Mixed = preg_replace("#\[thread\]([\d]+)\[/thread\]#si", '<a href="/discussion/\\1">/discussion/\\1</a>', $Mixed);
        $Mixed = preg_replace("#\[thread=[\"']?([\d]+)[\"']?\](.*?)\[/thread\]#si", '<a href="/discussion/\\1">\\2</a>', $Mixed);
        $Mixed = preg_replace("#\[post\]([\d]+)\[/post\]#si", '<a href="/discussion/comment/\\1#Comment_\\1">/discussion/comment/\\1</a>', $Mixed);
        $Mixed = preg_replace("#\[post=[\"']?([\d]+)[\"']?\](.*?)\[/post\]#si", '<a href="/discussion/comment/\\1#Comment_\\1">\\2</a>', $Mixed);
        $Mixed = preg_replace("#\[size=[\"']?(.*?)[\"']?\]#si", '<font size="\\1">', $Mixed);
        $Mixed = preg_replace("#\[font=[\"']?(.*?)[\"']?\]#si", '<font face="\\1">', $Mixed);
        $Mixed = preg_replace("#\[color=[\"']?(.*?)[\"']?\]#si", '<font color="\\1">', $Mixed);
        $Mixed = str_ireplace(array("[/size]", "[/font]", "[/color]"), "</font>", $Mixed);
        $Mixed = str_ireplace(array('[indent]', '[/indent]'), array('<div class="Indent">', '</div>'), $Mixed);
        $Mixed = str_ireplace(array("[left]", "[/left]"), '', $Mixed);
        $Mixed = preg_replace_callback("#\[list\](.*?)\[/list\]#si", array('ExtensibleBBCodePlugin', 'listCallback'), $Mixed);

        $Mixed = ExtensibleBBCodePlugin::customFormat($Mixed);

		$Result = Gdn_Format::html($Mixed);

		return $Result;

	}

}

?>