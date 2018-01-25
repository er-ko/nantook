<?php

// RENDER CATEGORY LIST
function categoryListRender($lang)
{
	require(CONFIG_PATH);
	$stmt = $mysqli->prepare('SELECT c.`id`, c.`parent_id`, c.`status`, i.`name`, i.`url`, c.`priority`
			FROM '. DB_TABLE_CATEGORY .' c
			LEFT JOIN '. DB_TABLE_CATEGORY_INFO .' i ON c.`id` = i.`category_id`
			WHERE i.`lang_code` = ?
			GROUP BY c.`id`, i.`name`, i.`url`
			ORDER BY c.`priority` ASC');
	$stmt->bind_param("s", $lang);
	$stmt->execute();
	$stmt->bind_result($id, $parent_id, $status, $name, $url, $priority);
	$ids = '';
	echo ('<ul>');
	while ($row = $stmt->fetch())
	{
		if ($parent_id == 0)
		{
			echo ('<li class="'. ($status == true ? 'category-active' : 'category-noactive') .'">');
			echo ('<span class="btn btn-sm btn-warning" style="padding: 2px 5px !important; margin-left: 5px;">'. ($status == true ? '<i class="fa fa-eye" aria-hidden="true" title="Kategorie je viditelná"></i>' : '<i class="fa fa-eye-slash" aria-hidden="true" title="Kategorie je skrytá"></i>') .'</span>');
			echo ('<a class="btn btn-sm btn-primary" style="padding: 3px 5px !important; margin-left: 5px;" href="index.php?shop-category&edit&category-id='. $id .'" title="Upravit kategorii"><i class="fa fa-edit" aria-hidden="true"></i></a>');
			echo ('<a class="btn btn-sm btn-danger" style="padding: 3px 6px !important; margin-left: 5px;" href="index.php?shop-category&remove&category-id='. $id .'" title="Odstranit kategorii" data-confirm="Opravdu si přejete odstranit vybranou kategorii: '. htmlspecialchars($name) .'?"><i class="fa fa-times" aria-hidden="true"></i></a>');
			echo ('<span class="category-list-id" style="margin: 0 10px 0 20px;">('. $id .')</span>'. htmlspecialchars($name) .'</span>');
			echo ('<span style="margin: 0 5px; font-size: 0.8em;">('. $url .')&nbsp;|&nbsp;('. $priority .')</span>');
			$ids = $id;
			categorySubcategoryListRender($row, $ids, $lang);
			echo ('</li>');
		}
	}
	echo ('</ul>');
	$stmt->close();
	$mysqli->close();
}

// RENDER SUBCATEGORIES IN CATEGORY LIST
function categorySubcategoryListRender($row, $ids, $lang)
{
	require(CONFIG_PATH);
	$stmt = $mysqli->prepare('SELECT c.`id`, c.`parent_id`, c.`status`, i.`name`, i.`url`, c.`priority`
			FROM '. DB_TABLE_CATEGORY .' c
			LEFT JOIN '. DB_TABLE_CATEGORY_INFO .' i ON c.`id` = i.`category_id`
			WHERE i.`lang_code` = ?
			GROUP BY c.`id`, i.`name`, i.`url`
			ORDER BY c.`priority` ASC');
	$stmt->bind_param("s", $lang);
	$stmt->execute();
	$stmt->bind_result($id, $parent_id, $status, $name, $url, $priority);
	echo ('<ul>');
	while ($row = $stmt->fetch())
	{
		if ($parent_id == $ids)
		{
			echo ('<li class="'. ($status == true ? 'category-active' : 'category-noactive') .'">');
			echo ('<span class="btn btn-sm btn-warning" style="padding: 2px 5px !important; margin-left: 5px;">'. ($status == true ? '<i class="fa fa-eye" aria-hidden="true"></i>' : '<i class="fa fa-eye-slash" aria-hidden="true"></i>') .'</span>');
			echo ('<a class="btn btn-sm btn-primary" style="padding: 2px 5px !important; margin-left: 5px;" href="index.php?shop-category&edit&category-id='. $id .'" title="Upravit kategorii"><i class="fa fa-edit" aria-hidden="true"></i></a>');
			echo ('<a class="btn btn-sm btn-danger" style="padding: 2px 6px !important; margin-left: 5px;" href="index.php?shop-category&remove&category-id='. $id .'" title="Odstranit kategorii" data-confirm="Opravdu si přejete odstranit vybranou kategorii: '. htmlspecialchars($name) .'?"><i class="fa fa-times" aria-hidden="true"></i></a>');
			echo ('<span class="category-list-id" style="margin: 0 10px 0 20px;">('. $id .')</span>'. htmlspecialchars($name) .'</span>');
			echo ('<span style="margin: 0 5px; font-size: 0.8em;">('. $url .')&nbsp;|&nbsp;('. $priority .')</span>');
			categorySubcategoryListRender($row, $id, $lang);
			echo ('</li>');
		}
	}
	echo ('</ul>');
	$stmt->close();
	$mysqli->close();
}

// RENDER CATEGORY EDIT DATA
function categoryEditRender($categoryId, $lang)
{
	require(CONFIG_PATH);
	$stmt = $mysqli->prepare('SELECT c.`id`, c.`parent_id`, c.`status`, c.`image`, c.`priority`, i.`name`, i.`content`, i.`url`, i.`meta_title`, i.`meta_description`, i.`h1_title`
			FROM '. DB_TABLE_CATEGORY .' c
			LEFT JOIN '. DB_TABLE_CATEGORY_INFO .' i ON c.`id` = i.`category_id`
			WHERE c.`id` = ? AND i.`lang_code` = ?');
	$stmt->bind_param("is", $categoryId, $lang);
	$stmt->execute();
	$stmt->bind_result($id, $parent_id, $status, $image, $priority, $name, $content, $url, $meta_title, $meta_description, $h1_title);
	$stmt->fetch();
	$stmt->close();
	$mysqli->close();
	return array($id, htmlspecialchars($name), $content, $url, $meta_title, $meta_description, $image, $status, $priority, $parent_id, $h1_title);
}

// RENDER SELECT CATEGORIES - WITHOUT SELECTED
function categoriesAddProductRender($lang)
{
	require(CONFIG_PATH);
	$stmt = $mysqli->prepare('SELECT c.`id`, i.`name`
			FROM '. DB_TABLE_CATEGORY .' c
			LEFT JOIN '. DB_TABLE_CATEGORY_INFO .' i ON c.`id` = i.`category_id`
			WHERE i.`lang_code` = ?
			ORDER BY c.`id` ASC');
	$stmt->bind_param("s", $lang);
	$stmt->execute();
	$stmt->bind_result($id, $name);
	while ($row = $stmt->fetch())
	{
		echo ('<option value="'. $id .'">('. $id .') '. htmlspecialchars($name) .'</option>');
	}
	$stmt->close();
	$mysqli->close();
}

// RENDER SELECT CATEGORIES - WITH SELECTED
function categoriesSelectRender($categoryId, $parent_category, $lang)
{
	require(CONFIG_PATH);
	$stmt = $mysqli->prepare('SELECT c.`id`, i.`name`
			FROM '. DB_TABLE_CATEGORY .' c
			LEFT JOIN '. DB_TABLE_CATEGORY_INFO .' i ON c.`id` = i.`category_id`
			WHERE i.`lang_code` = ? AND c.`id` != ?
			ORDER BY c.`id` ASC');
	$stmt->bind_param("si", $lang, $categoryId);
	$stmt->execute();
	$stmt->bind_result($id, $name);
	while ($row = $stmt->fetch())
	{
		echo ('<option value="'. $id .'"'. ($id == $parent_category ? 'selected' : '') .'>('. $id .') '. htmlspecialchars($name) .'</option>');
	}
	$stmt->close();
	$mysqli->close();
}

// CATEGORY UPDATE DATA
function categoryUpdateData($parent_id, $status, $image, $priority, $date_updated, $id)
{
	require(CONFIG_PATH);
	if (!empty($image))
		$imageUpdate = 'images/' . $image;
	else
		$imageUpdate = '';
	$stmt = $mysqli->prepare('UPDATE '. DB_TABLE_CATEGORY .' SET `parent_id` = ?, `status` = ?, `image` = ?, `priority` = ?, `date_updated` = ? WHERE `id` = ?');
	$stmt->bind_param("iisisi", $parent_id, $status, $imageUpdate, $priority, $date_updated, $id);
	$stmt->execute();
	$stmt->close();
	$mysqli->close();
}

// CATEGORY UPDATE INFO
function categoryUpdateInfo($name, $content, $url, $meta_title, $meta_description, $h1_title, $id, $lang)
{
	require(CONFIG_PATH);
	$stmt = $mysqli->prepare('UPDATE '. DB_TABLE_CATEGORY_INFO .' SET `name` = ?, `content` = ?, `url` = ?, `meta_title` = ?, `meta_description` = ?, `h1_title` = ? WHERE `category_id` = ? AND `lang_code` = ?');
	$stmt->bind_param("ssssssis", $name, $content, $url, $meta_title, $meta_description, $h1_title, $id, $lang);
	$stmt->execute();
	$stmt->close();
	$mysqli->close();
}

// CATEGORY GET LAST INSERT ID
function categoryGetLastInsertId()
{
	require(CONFIG_PATH);
	$stmt = $mysqli->prepare('SELECT `id` FROM '. DB_TABLE_CATEGORY .' ORDER BY `id` DESC LIMIT 1');
	$stmt->execute();
	$stmt->bind_result($id);
	$stmt->fetch();
	$stmt->close();
	$mysqli->close();
	return $id;
}

// CATEGORY ADD DATA
function categoryAddData($parent_id, $status, $image, $priority, $date_created)
{
	require(CONFIG_PATH);
	$newId = mysqli_insert_id($mysqli);
	if (!empty($image))
		$imageAdd = 'images/'. $image;
	else
		$imageAdd = '';
	$stmt = $mysqli->prepare('INSERT INTO '. DB_TABLE_CATEGORY .' (`id`, `parent_id`, `status`, `image`, `priority`, `date_updated`, `date_created`) VALUES (?, ?, ?, ?, ?, ?, ?)');
	$stmt->bind_param("iiisiss", $newId, $parent_id, $status, $imageAdd, $priority, $date_created, $date_created);
	$stmt->execute();
	$stmt->close();
	$mysqli->close();
}

// CATEGORY ADD INFO
function categoryAddInfo($lang, $name, $content, $url, $meta_title, $meta_description, $h1_title)
{
	require(CONFIG_PATH);
	$lastId = categoryGetLastInsertId();
	$newId = mysqli_insert_id($mysqli);
	$stmt = $mysqli->prepare('INSERT INTO '. DB_TABLE_CATEGORY_INFO .' (`id`, `lang_code`, `category_id`, `name`, `content`, `url`, `meta_title`, `meta_description`, `h1_title`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
	$stmt->bind_param("isissssss", $newId, $lang, $lastId, $name, $content, $url, $meta_title, $meta_description, $h1_title);
	$stmt->execute();
	$stmt->close();
	$mysqli->close();
}

// CATEGORY REMOVE DATA
function categoryRemoveData($categoryId)
{
	require(CONFIG_PATH);
	$stmt = $mysqli->prepare('DELETE FROM '. DB_TABLE_CATEGORY .' WHERE `id` = ?');
	$stmt->bind_param("i", $categoryId);
	$stmt->execute();
	$stmt->close();
	$mysqli->close();
}

// CATEGORY REMOVE INFO
function categoryRemoveInfo($categoryId)
{
	require(CONFIG_PATH);
	$stmt = $mysqli->prepare('DELETE FROM '. DB_TABLE_CATEGORY_INFO .' WHERE `category_id` = ?');
	$stmt->bind_param("i", $categoryId);
	$stmt->execute();
	$stmt->close();
	$mysqli->close();
}
