<?php

class Direction
{
	public $id;
	public $step;
	public $stepNum;

	
	function Direction($step, $stepNum)
	{
		$this->id = $id;
		$this->step = $step;
		$this->stepNum = $stepNum;
		$this->orig_step = $orig_step;
		$this->ver_note = $ver_note;
	}
	

static function process($count, $line)
{
		
		if (strlen($line) == 0)
			return NULL;
			
		//$line = preg_replace('/[(\x00-\x1F)]*/','',$line);
		//$line = preg_replace('/[^(\x00-\x20)]*/','',$line);
		$expr = "/\s*[\-\*\#\d\).]*\s*(.*)/u";
		preg_match($expr,$line,$matches);
		$step = $matches[1];
		
		
		
		return $step;
}
}

class Ingredient
{
	public $id;
	public $recipe_id;
	public $note;
	public $prep;
	public $fooditem;
	public $unit;
	public $quantity;
	public $line;
	public $highlighted;
	public $header;
		
	function Ingredient($id, $note, $prep, $fooditem, $unit, $quantity, $line, $header, $step)
	{
		$this->id = $id;
		$this->note = $note;
		$this->prep = $prep;
		$this->fooditem = $fooditem;
		$this->unit = $unit;
		$this->quantity = $quantity;
		$this->line = $line;
		$this->header = $header;
		$this->step = $step;
	}
	
	
public function add_to_database($recipe_id)
{
	global $wpdb;

	$sql = "INSERT INTO $wpdb->recipe_schema_ingredients (quantity, unit, fooditem, recipe_id, preperation, note, line, header, step) VALUES (%s,%s,%s,%d,%s,%s,%s, %d, %d)"; 
	$q = $wpdb->prepare($sql,$this->quantity,$this->unit, $this->fooditem, $recipe_id, $this->prep, $this->note, $this->line, $this->header, $this->step);
	$wpdb->query($q);

	$this->id = $wpdb->insert_id;
	}

static function delete_recipe_ingredients($recipe_id)
{
	global $wpdb;
		

	$sql = "DELETE FROM $wpdb->recipe_schema_ingredients WHERE recipe_id = '$recipe_id'"; 
	$result = $wpdb->query($sql);
	return true;
}

static function process($line, $step)
{
	global $wpdb;
	
	if (strlen($line) == 0)
			return NULL;
		
		$expr = "/^=([\w\s\d\.\+\?\*\$\(\)\[\]\#:]+)/u";
		$matched = preg_match($expr,$line,$matches);
		if ($matched) {
		
			$ingredient = new Ingredient(-1, '', '', '', '', '',  $matches[1], true, $step);
			$ingredient->highlighted = "";
		
		} else {
		
		$unit_choices = "large|medium|small|ounce|oz|oz.|ml|pound|lb|cup|tbs|tsp|tbs.|tsp.|tablespoon|teaspoon|slice|scoop|pinch|sprig|can|bottle|bundle|clove|handful";
		$expr = "/(\s*[\d\-\/ ]+)?\s*(\b(".$unit_choices.")(s|es)?\b)?\s*([\w\d\-\.: ]*)(, [\w\d\,\. ]*)?(\([\w\d\- ]*\))?/ui";
		
		//$line = preg_replace('/[(\x7F-\xBB)]*/','',$line);
		$line = preg_replace('/^[#-\* ]*/u','',$line);
		
		preg_match($expr,$line,$matches);
		//print_r($matches);
		$highlighted = $line;
		$quant="";
		$unit="";
		$item="";
		$prep="";
		$note="";
		
		$match_num = count($matches);
		
		
		if ($match_num > 1) 
		{
			$quant = $matches[1];
			if ($quant)
			{
				$start = strpos($highlighted, $quant);
				$end = $start + strlen($quant);
				$highlighted = substr_replace($highlighted, "</span>", $end, 0);
				$highlighted = substr_replace($highlighted, "<span id='ingQuantity'>",$start,0);
			}
		}
		if ($match_num > 2)
		{
			$unit = $matches[2];
			if ($unit)
			{
				$start = strpos($highlighted, $unit);
				$end = $start + strlen($unit);
				$highlighted = substr_replace($highlighted, "</span>", $end, 0);
				$highlighted = substr_replace($highlighted, "<span id='ingUnit'>",$start,0);	
			}
		}
		if ($match_num > 5)
		{
			$item = $matches[5];
			if ($item)
			{
				$start = strpos($highlighted, $item);
				$end = $start + strlen($item);
				$highlighted = substr_replace($highlighted, "</span>", $end, 0);
				$highlighted = substr_replace($highlighted, "<span id='ingItem'>",$start,0);
			}
		}
		
		if ($match_num > 6)
		{
			$prep = $matches[6];
			if ($prep)
			{
				$start = strpos($highlighted, $prep);
				$end = $start + strlen($prep);
				$highlighted = substr_replace($highlighted, "</span>", $end, 0);
				$highlighted = substr_replace($highlighted, "<span id='ingPrep'>",$start,0);
			}
		
		}
		
		if ($match_num > 7)
		{
			$note = $matches[7];
			if ($note)
			{
				$start = strpos($highlighted, $note);
				$end = $start + strlen($note);
				$highlighted = substr_replace($highlighted, "</span>", $end, 0);
				$highlighted = substr_replace($highlighted, "<span id='ingNote'>",$start,0);
			}
		}

		$ingredient = new Ingredient(-1, $note, $prep, $item, $unit, $quant, $line, false, $step);
		$ingredient->highlighted = $highlighted;
		}
		
		return $ingredient;
}



}


class Recipe
{
 public $title;
 public $source;
 public $source_url;
 public $ingredients = array();
 public $directions = array();
 public $equipment = array();
 public $keywords = array();
 public $active_time;
 public $total_time;
 public $permalink;
 public $author;
 public $blurb;
 public $yield;
 public $id;
 public $private;
 public $ver_note;
 public $parent_recipe;
 public $username;
 public $category;
 
 
 /*
 static function get_recipe_preview($recipe_id)
{
	global $session;
	global $database;

	$q = "SELECT id, title, author, source, source_url FROM recipes WHERE id = :id";
    $stmt = $database->db->prepare($q);
    $stmt->execute(array(':id' => $recipe_id));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);     
    
 	if (!$row)
	{
		return NULL;
	}
 	
	$recipe = new Recipe();
	$recipe->title = $row['title'];
	$recipe->source = $row['source'];
	$recipe->source_url = $row['source_url'];
	$recipe->author = $row['author'];
	$recipe->id = $row['id'];
	
	return $recipe;
}

static function get_recipe($recipe_id)
{
	global $session;
	global $database;

	$q = "SELECT * FROM recipes WHERE id = :id";
    $stmt = $database->db->prepare($q);
    $stmt->execute(array(':id' => $recipe_id));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);     
    
	if (!$row)
	{
		return NULL;
	}
	
	if ($row["username"] != $session->username)
	{
		if ($row["private"] == TRUE)
		{
			return NULL;	
		}
	}
	
	$recipe = new Recipe();
	$recipe->title = $row['title'];
	$recipe->source = $row['source'];
	$recipe->source_url = $row['source_url'];
	$recipe->active_time = $row['active_time'];
	$recipe->total_time = $row['total_time'];
	$recipe->author = $row['author'];
	$recipe->blurb = $row['blurb'];
	$recipe->yield = $row['yield'];
	$recipe->id = $row['id'];
	$recipe->parent_recipe = $row['parent_recipe'];
	$recipe->ver_note = $row['ver_note'];
	$recipe->permalink = $row['permalink'];
	$recipe->username = $row['username'];
	$recipe->category = $row['category'];
	
	$q = "SELECT * FROM ingredients WHERE recipe = :id";
	$stmt = $database->db->prepare($q);
    $stmt->execute(array(':id' => $recipe_id));
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$q = "SELECT * FROM fooditems WHERE id = :id";
		$food_stmt = $database->db->prepare($q);
    	$food_stmt->execute(array(':id' => $row['fooditem']));
		$food_row = $food_stmt->fetch(PDO::FETCH_ASSOC);
	
		$q = "SELECT * FROM measurements WHERE id = :id";
		$measurement_stmt = $database->db->prepare($q);
		$measurement_stmt->execute(array(':id' => $row['unit']));
		$measurement_row = $measurement_stmt->fetch(PDO::FETCH_ASSOC);
	
		$ingredient = new Ingredient($row['id'], $row['note'], $row['preperation'], $food_row['fooditem'], $measurement_row['unit'], $row['quantity'],  $row['line'], $row['orig_line'], $row['ver_note']);
		$recipe->AddIngredient($ingredient);
	}
	
	$q = "SELECT * FROM equipment WHERE recipe = :id";
	$equipment_stmt = $database->db->prepare($q);
  	$equipment_stmt->execute(array(':id' => $recipe_id));
	while ($row = $equipment_stmt->fetch(PDO::FETCH_ASSOC))
	{
	
		$equipment = new Equipment($row['id'], $row['item'], $row['orig_item'], $row['ver_note']);
		$recipe->AddEquipment($equipment);
	}
	
	$q = "SELECT * FROM directions WHERE recipe= :id ORDER BY step_num";
	$direction_stmt = $database->db->prepare($q);
    $direction_stmt->execute(array(':id' => $recipe_id));
	while ($row = $direction_stmt->fetch(PDO::FETCH_ASSOC))
	{
		$direction = new Direction($row['id'], $row['step'], $row['step_num'],$row['orig_step'],$row['ver_note']);
		$recipe->AddDirection($direction);
	}
	
	
	$keywords = $database->GetRecipeKeywords($recipe_id);
	
	
	$recipe->AddKeywords($keywords);
	return $recipe;
} 
 
static function get_recipe_username($recipe_id)
{
	global $database;
	$q = "SELECT username FROM recipes WHERE id = :id";
    $stmt = $database->db->prepare($q);
    $stmt->execute(array(':id' => $recipe_id));
	$stmt->bindColumn('username', $username);
	$stmt->fetch(PDO::FETCH_ASSOC);
	
	return $username;	
}



static function recipe_exists($recipe_id)
{
	global $database;
	$q = "SELECT EXISTS (SELECT 1 FROM recipes WHERE id = :id)";
    $stmt = $database->db->prepare($q);
    $stmt->execute(array(':id' => $recipe_id));
	$row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
	if ($row == 1)
	return true;
	else
	return false;
}



 function Recipe()
 {
	 $this->ver_note = NULL;
	$this->parent_recipe = NULL;
 }
 
 
 function AddIngredient($ingredient)
 {
	$this->ingredients[] = $ingredient; 
 }

function AddEquipment($equipment)
{
	$this->equipment[] = $equipment;	
}

function AddDirection($direction)
{
	$this->directions[] = $direction;	
}
	
function AddKeywords($keywords)
{
	$this->keywords = $keywords;
}

function update_permalink()
{
	global $database;
	
		$q = "UPDATE recipes SET permalink=:permalink WHERE id=:id";
		$stmt = $database->db->prepare($q);
		$result = $stmt->execute(array(':permalink' => $this->permalink, ':id' => $this->id));
}

function update_database()
{
	global $database;
	$this->private = FALSE;
	
	$q = "UPDATE recipes SET 
		title=:title, 
		blurb=:blurb, 
		source= :source, 
		source_url=:source_url, 
		author=:author, 
		ver_note=:ver_note,
		permalink=:permalink,
		category=:category
		WHERE id= :id";
	$stmt = $database->db->prepare($q);
	 $stmt->execute(array(':title' => $this->title, ':blurb' => $this->blurb , ':source' => $this->source, ':id' => $this->id, ':source_url' => $this->source_url, ':author' => $this->author, ':ver_note' => $this->ver_note, ':permalink' => $this->permalink, ':category' => $this->category));

	if (!empty($this->ingredients))
	{
		foreach($this->ingredients as $ing)
		{
			$ing->add_to_database($this->id);			
		}
	}
	
	if (!empty($this->equipment))
	{
		foreach ($this->equipment as $item)
		{
			$item->add_to_database($this->id);
		}
	}
	
	
	if (!empty($this->directions))
	{
		foreach ($this->directions as $direction)
		{
			$direction->add_to_database($this->id);
		}
	}

	
}


function add_to_database()
{
	global $database;
	global $session;
	
	$this->private = FALSE;
	
	$q = "INSERT INTO recipes 
	(title, blurb, source, source_url, author, username, private, ver_note, parent_recipe, permalink, category) 
	VALUES (:title, :blurb, :source, :source_url, :author, :username, :private, :ver_note, :parent_recipe, :permalink, :category)"; 
	$stmt = $database->db->prepare($q);
	$stmt->execute(array(':title' => $this->title, ':blurb' => $this->blurb,':source' =>$this->source, ':source_url' => $this->source_url, ':author' => $this->author, ':username' => $session->username, ':private' => $this->private, ':ver_note' => $this->ver_note, ':parent_recipe' => $this->parent_recipe, ':permalink' => $this->permalink, ':category' => $this->category));
	$this->id = $database->db->lastInsertId();
	
	
	
	
	foreach($this->keywords as $key)
	{
		$database->AddKeyword($this->id, $key);
	}
	
	if (!empty($this->ingredients))
	{
		foreach($this->ingredients as  $ingredient)
		{
			 $ingredient->add_to_database($this->id);	
			
		}
	}
	
	if (!empty($this->equipment))
	{
		foreach ($this->equipment as $item)
		{
			$item->add_to_database($this->id);		
		}
	}
	
	if (!empty($this->directions))
	{
		foreach ($this->directions as $direction)
		{
			$direction->add_to_database($this->id);
		}
	}
}

function delete_from_database()
{
	Recipe::delete_recipe($this->id);
}
static function delete_recipe($recipe_id)
{
	global $session;
	global $database;

	$username = Recipe::get_recipe_username($recipe_id);
	
	if (!$session->isAdmin() && ($username != $session->username))
	{
		return false;	
	}
	
	$sql = "DELETE FROM ingredients WHERE recipe = '$recipe_id'"; 
	$result = $database->db->query($sql);
	$sql = "DELETE FROM equipment WHERE recipe = '$recipe_id'"; 
	$result = $database->db->query($sql);
	$sql = "DELETE FROM directions WHERE recipe = '$recipe_id'"; 
	$result = $database->db->query($sql);
	$sql = "DELETE FROM recipes WHERE id = '$recipe_id'"; 
	$result = $database->db->query($sql);
	$sql = "DELETE FROM recipes_keywords WHERE recipe_id = '$recipe_id'"; 
	$result = $database->db->query($sql);
	
	//Delete any bookmarks that users may have set for this recipe
	$q = "DELETE FROM bookmarks WHERE username = :username";
	$stmt = $database->db->prepare($q);
	$stmt->execute(array(':username' => $username));
	$database->DeleteRecipeAccess($recipe_id);
	
	//Delete Recipe Versions based on this recipe
	$rows = $database->GetRecipeVersions($recipe_id);
	foreach ($rows as $row)
	{	
		Recipe::delete_recipe($row["id"]);
	}
	return true;
}


function GetKeywords()
{
	$tags_to_edit = join( ',', $this->keywords );
	$tags_to_edit =  $tags_to_edit; //esc_attr( $tags_to_edit );
	return $tags_to_edit;
		
}*/
}

?>