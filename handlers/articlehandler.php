<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/Settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/MySQL.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/objects/Article.php';

class ArticleHandler {
	// Get article.
	public static function getArticle($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_articles . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new Article($row['id'], $row['name'], $row['title'], $row['content'], $row['image'], $row['author'], $row['datetime']);
		}
	}
	
	// Get article by name article.
	public static function getArticleByName($name) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_articles . ' WHERE name=\'' . $name . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return self::getArticle($row['id']);
		}
	}
	
	// Get a list of all articles.
	public static function getArticles() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_articles);
		$articleList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($articleList, self::getArticle($row['id']));
		}
		
		MySQL::close($con);

		return $articleList;
	}
}
?>