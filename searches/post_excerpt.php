<?php

class SearchPostExcerpt extends Search
{
	function find ($pattern, $limit, $offset, $orderby)
	{
		global $wpdb;

		$results = array ();
		$posts   = $wpdb->get_results ("SELECT ID, post_title, post_excerpt FROM {$wpdb->posts} WHERE post_status != 'inherit' ORDER BY ID $orderby LIMIT $offset,$limit");
		if (count ($posts) > 0)
		{
			foreach ($posts AS $post)
			{
				if (($matches = $this->matches ($pattern, $post->post_excerpt, $post->ID)))
				{
					foreach ($matches AS $match)
						$match->title = $post->post_title;

					$results = array_merge ($results, $matches);
				}
			}
		}

		return $results;
	}

	function get_options ($result)
	{
		$options[] = '<a href="'.get_permalink ($result->id).'">'.__ ('view', 'search-regex').'</a>';
		if ($result->replace)
			$options[] = '<a href="#" onclick="regex_replace (\'SearchPostExcerpt\','.$result->id.','.$result->offset.','.$result->length.',\''.str_replace ("'", "\'", $result->replace_string).'\'); return false">replace</a>';

		if (current_user_can ('edit_post', $result->id))
			$options[] = '<a href="'.get_bloginfo ('wpurl').'/wp-admin/post.php?action=edit&amp;post='.$result->id.'">'.__ ('edit','search-regex').'</a>';
		return $options;
	}

	function show ($result)
	{
		printf (__ ('Post #%d: %s', 'search-regex'), $result->id, $result->title);
	}

	function name () { return __ ('Post excerpt', 'search-regex'); }

	function get_content ($id)
	{
		global $wpdb;

		$post = $wpdb->get_row ("SELECT post_excerpt FROM {$wpdb->prefix}posts WHERE id='$id'");
		return $post->post_excerpt;
	}

	function replace_content ($id, $content)
	{
		global $wpdb;
		$content = wpdb::escape ($content);
		$wpdb->query ("UPDATE {$wpdb->posts} SET post_excerpt='{$content}' WHERE ID='$id'");
	}
}



?>