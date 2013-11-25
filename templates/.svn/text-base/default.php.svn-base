<?php
// This function is called to generate a text representation of a recipe. The template function
// takes in an Object which represents all the data from the recipe and return a block of text.
function recipe_text( $item ) {

	// result is variable that gets returned with the text of the recipe
	
	// Generates the Title for the recipe
	$result = '<h2><span itemprop="name">' . $item['title'] . '</span></h2>';

	// Prints out the Source and wraps it in a URL if provided
	if ( isset( $item['source'] ) ) {
		$result = $result . '<div class="recipe-meta"><b>From:</b> ';
		if ( isset( $item['source_url'] ) ) {
			//If a source URL is provided
			$result = $result . '<a href="' . $item['source_url'] . '">' . $item['source'] . '</a>';
		} else {
			$result = $result . $item['source'];
		}
		$result = $result . '</div>';
	}



	$result = $result . '<div class="recipe-meta">';

	// Prints out the associated times, formatting of the times is done in recipe-schema-client.php, print_time() 
	if ( isset( $item['prep_time'] ) )
		$result = $result . 'Prep Time: ' . $item['prep_time'];
	if ( isset( $item['cooking_time'] ) )
		$result = $result . 'Cooking Time: ' . $item['cooking_time'];
	if ( isset( $item['total_time'] ) )
		$result = $result . 'Total Time: ' . $item['total_time'];

	$result = $result . '</div><div class="recipe-meta">';


	// Handles additional Meta info
	if ( isset( $item['yield'] ) )
		$result = $result . '<b>Yields:</b> <span itemprop="recipeYield">' . $item['yield'] . '</span>';
	if ( isset( $item['category'] ) )
		$result = $result . ' <b>Category:</b> <span itemprop="recipeCategory">' . $item['category'] . '</span>';
	if ( isset( $item['cuisine'] ) )
		$result = $result . ' <b>Cuisine:</b> <span itemprop="recipeCuisine">' . $item['cuisine'] . '</span>';
	$result = $result . '</div>';

	// Ingredients section
	if ( $item['ingredients'] ) {
		$result = $result . '<b>Ingredients:</b><br><ul>';

		// Itterates through the ingredients
		foreach ( $item['ingredients'] as $ingredient ) {
			if ( $ingredient->header ) {

				// if the current item is a Header, end the list, make the header bold and start a new list
				$result = $result . '</ul><b>' . stripslashes( $ingredient->line ) . '</b><ul>';
			} else {
				$result = $result . '<li><span itemprop="ingredients">' . stripslashes( $ingredient->line ) . '</span></li>';
			}
		}


		$result = $result . '</ul>';
	}


	// Directions Section
	if ( $item['directions'] ) {
		$result = $result . '<b>Directions:</b><br><ol>';

		// Itterates through the directions
		foreach ( $item['directions'] as $direction ) {

			if ( $direction[0] == "=" ) {

				// if the current item is a Header, end the list, make the header bold and start a new list
				$result = $result . '</ol><b>' . stripslashes( substr( $direction, 1, strlen( $direction )-1 ) ) . '</b><ol>';
			} else {
				$result = $result . '<li><span itemprop="recipeInstructions">' . stripslashes( $direction ) . '</span></li>';
			}
		}


		$result = $result . '</ol>';
	}

	// Finally, print out any notes
	if ( isset( $item['notes'] ))
		$result = $result . '<b>Notes:</b><br>' . $item['notes'];

	return $result;
}






	
