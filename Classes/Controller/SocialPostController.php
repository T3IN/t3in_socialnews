<?php
namespace T3IN\T3inSocialnews\Controller;
/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Vikram Mandal <info@t3in.com>, FiveE Technologies
 *
 *  All rights reserved
 *
 ***************************************************************/

/**
 *
 * @package T3IN_Socialpost
 */
class SocialPostController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {


	/**
	 * action list
	 * @return void
	 */
	public function listAction() {		
		$res_news = $this->getNewsList();
		if ($res_news['status']){
			$this->view->assign('news', $res_news['data']);
			}else {
				$this->view->assign('error', $res_news['error']);
				}					
	}


	/**
	 * Get news from News RSS 
	 * 
	 */
	function getNewsList() {
		$res = array('status'=>false);
		$res['data'] = array();
		$feedUrl = $this->settings['newsXmlUrl'];
		if($feedUrl=='') {
			$res['error']  = 'News related: config var url_xml is empty. Please check  socialpost /Records Type settings';
			$res['status'] = false;
			return $res;			
			}		
			
		$rss = new \DOMDocument();
		if(!$rss->load($feedUrl)) {
			$res['error']  = 'Could not access url = ' . $feedUrl;
			$res['status'] = false;
			return $res;
		}
		$feed = array();
		foreach ($rss->getElementsByTagName('item') as $node) {
			$item = array (
				'uid' => $node->getElementsByTagName('uid')->item(0)->nodeValue,
				'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
				'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
				'pubDate' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
				'description' => $node->getElementsByTagName('description')->item(0)->nodeValue,
				'content' => $node->getElementsByTagName('encoded')->item(0)->nodeValue
			);
			array_push($feed, $item);
		}						
		$res['data'] = $feed;
		$res['status'] = true;
		return $res;	

	}


	/**
	 * action socialPost
	 * @return void
	 */
	public function socialPostAction() {

		if ($this->request->hasArgument('newsItem')) {
			if(is_array($this->request->getArgument('newsItem'))) {
				$newsItemUids = implode(',', $this->request->getArgument('newsItem'));
				$this->view->assign('newsItemUids', $newsItemUids);
				}
		}			

		$res_news = $this->getNewsList();
		$newsSelected = array();
		if ($res_news['status']){
			foreach($res_news['data'] as $item){				
				if(strpos($newsItemUids, $item['uid']) !== false) {
					$newsSelected[] = $item['title'];
					//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($item);
				}
			}
			$this->view->assign('newsSelected', $newsSelected);
		}else {
			$this->view->assign('error', $res_news['error']);
		}

					
		if ($this->request->hasArgument('socialSite')) {
			$socialSite = $this->request->getArgument('socialSite');
			$this->view->assign('socialSite', $socialSite);
			
			switch($socialSite) {
				case 'facebook':
						$userAccessToken = $this->request->getArgument('userAccessToken');
						if ($userAccessToken === false){
							$this->addFlashMessage('No token found. re-login to FB');  
							return;
						}else {
							$this->getfacebookPages($userAccessToken);
						}		
						break;
				}		
			}//end-if
	}				


	/**
	 * action facebookPagePost
	 * @return void
	 */
	public function facebookPagePostAction() {
	
		if ($this->request->hasArgument('fbPage')) {
			$fbPageToken = $this->request->getArgument('fbPage');
			$app_id = $this->settings['app_id'];		
			$app_secret = $this->settings['app_secret'];			
			
			$fb = new \Facebook\Facebook([  
			  'app_id' => $app_id,  
			  'app_secret' => $app_secret,  
			  'default_graph_version' => 'v2.11',
			  ]);  
			  
	
			// post news to the page://
			$newsItemUids = $this->request->getArgument('newsItemUids');
			if($newsItemUids == '') {
				$this->addFlashMessage('No news were selected.');
			}else {
				$fbPosts = $this->generateNewsData($newsItemUids); 
				foreach($fbPosts as $post){
					$fbPostError = false;
					$response = $fb->post('/me/feed', $post, $fbPageToken);
								
						try {
						  $response = $fb->getClient()->sendRequest($request);
						} catch(\Facebook\Exceptions\FacebookResponseException $e) {
						  $this->addFlashMessage($post['name'] . ' - Graph returned an error: ' . $e->getMessage());
						  $fbPostError = true;
						} catch(\Facebook\Exceptions\FacebookSDKException $e) {
						  $this->addFlashMessage($post['name'] . ' - Facebook SDK returned an error: ' . $e->getMessage());
						  $fbPostError = true;
						} 					
						
						if (!$fbPostError){
							$this->addFlashMessage($post['name'] . ' - posted to Facebook');
							}
					}//foreach			
					$this->addFlashMessage('Facebook news posting done.');			
			}
		}		
	}

	
	/*
	 *@param newsItemUids : comma seperated news ids
	 *@param newsTypeRecordUid : int uid
	 */
	function generateNewsData($newsItemUids) {
		$fbPosts = array();			
		if ($newsItemUids) {
			$newsItemUids = explode(',', $newsItemUids);
			$res_news = $this->getNewsList();
			if ($res_news['status']){
				foreach($res_news['data'] as $newsItem){
					if(in_array($newsItem['uid'], $newsItemUids)) {
						$fbNewsPost = array();
						$fbNewsPost['name'] = $newsItem['title'];
						$fbNewsPost['link'] = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv(TYPO3_SITE_URL)
														. $newsItem['link'];
						//$fbNewsPost['message'] = $newsItem['description'];	// teaser	
						$fbNewsPost['description'] = $newsItem['content'];
						$fbPosts[] = $fbNewsPost;
						}
					}
				}//if-status
			}
		return $fbPosts;	
	}
		

	/*
	* get list of Facebook pages under the Facebook user
	*/
	public function getfacebookPages($userAccessToken) {
			
		$app_id = $this->settings['app_id'];		
		$app_secret = $this->settings['app_secret'];			
		
		$fb = new \Facebook\Facebook([  
		  'app_id' => $app_id,  
		  'app_secret' => $app_secret,  
		  'default_graph_version' => 'v2.11',  
		  ]);  		  		  
		$fbApp = $fb->getApp();
		
			// Send the request to Graph
		$request = new \Facebook\FacebookRequest($fbApp, $userAccessToken, 'GET', '/me/accounts');
		try {
		  $response = $fb->getClient()->sendRequest($request);
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
		  $this->addFlashMessage('Graph returned an error: ' . $e->getMessage());
		  return;
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
		  $this->addFlashMessage('Facebook SDK returned an error: ' . $e->getMessage());
		  return;
		}
		$pagesEdge = $response->getGraphEdge(); 		
		$pagesSelectOptions = array();
		foreach ($pagesEdge as $page) {
			$pageA = $page->asArray();
			$option = new \stdClass();
    		$option->key = (string) $pageA['access_token'];
    		$option->value = $pageA['name'];
    		$pagesSelectOptions[] = $option;			
		}
		$this->view->assign('pagesSelectOptions', $pagesSelectOptions);
	}

	
}
