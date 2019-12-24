<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage SEO
 * @category Library
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 * 
 * @link https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/markup Twitter Markup
 * @link https://developers.facebook.com/docs/sharing/webmasters/#markup Facebook Markup
 */

class SEO
{
	protected $meta_list;
	protected $link_list;

	protected $meta_tag_template = "<meta {{attribute_key}}=\"{{attribute_value}}\" content=\"{{content}}\" {{other_attribute}}/>\n";
	protected $link_tag_template = "<link rel=\"{{attribute_value}}\" {{content}} />\n";

	/**
	 * Meta Tag Generator
	 * 
	 * @param  string  $attribute_key   attribute key
	 * @param  string  $attribute_value attribute value
	 * @param  string  $content         content Value
	 * @param  boolean $return          class
	 * @return object
	 */
	public function meta_tag($attribute_key = null, $attribute_value = null, $content = null, $return = true)
	{
		($this->validate_meta_attribute($attribute_key))?$this->run_validation($attribute_key,$attribute_value,$content):FALSE;
		return ($return)?$this:FALSE;
	}

	/**
	 * Meta Tag Render
	 *
	 * Render generated meta tag (return only meta tags)
	 * @return string
	 */
	public function meta_tag_render()
	{
		$output 	= '';
		$replace 	= ['{{attribute_key}}','{{attribute_value}}','{{content}}','{{other_attribute}}'];

		if (!empty($this->meta_list) && is_array($this->meta_list))
		{
			foreach ($this->meta_list as  $value)
			{
				if (isset($value['error_message']))
				{
					$other_attribute = 'error_message="'.$value['error_message'].'"';
				}
				else
				{
					$other_attribute = '';
				}

				$output .= str_replace($replace, array($value['attribute_key'], $value['attribute_value'],$value['content'],$other_attribute), $this->meta_tag_template);
			}
		}

		return $output;
	}

	/**
	 * Link Tag Generator
	 * 
	 * @param  string  $attribute_value attribute value
	 * @param  string  $content         attribute content
	 * @param  boolean $return          class
	 * @return object
	 */
	public function link_tag($attribute_value = null, $content = null, $return = true)
	{
		if ($attribute_value !== 'stylesheet')
		{
			$this->link_list[sha1($attribute_value)] = ['attribute_value' => $attribute_value,'content' => $content];
		}
		else
		{
			$this->link_list[sha1($content)] = ['attribute_value' => $attribute_value,'content' => $content];
		}

		return ($return)?$this:FALSE;
	}

	/**
	 * Render Link Tag (return only link tags)
	 * 
	 * @return string
	 */
	public function link_tag_render()
	{
		$output = '';
		$vars   = array('{{attribute_value}}','{{content}}');

		foreach ($this->link_list as $key => $value)
		{
			$output .= str_replace($vars, array($value['attribute_value'], $value['content']), $this->link_tag_template);
		}

		return $output;
	}

	/**
	 * Render Meta Tag & Link Tag
	 * 
	 * @return string
	 */
	public function render()
	{
		$output 	= '';
		$rmeta 		= ['{{attribute_key}}','{{attribute_value}}','{{content}}','{{other_attribute}}'];
		$rlink 		= ['{{attribute_value}}','{{content}}'];

		if (!empty($this->meta_list) && is_array($this->meta_list))
		{
			foreach ($this->meta_list as $value)
			{
				if (isset($value['error_message']))
				{
					$other_attribute = 'error_message="'.$value['error_message'].'"';
				}
				else
				{
					$other_attribute = '';
				}

				$output .= str_replace($rmeta,array($value['attribute_key'], $value['attribute_value'],$value['content'],$other_attribute), $this->meta_tag_template);
			}
		}

		if (!empty($this->link_list) && is_array($this->link_list))
		{
			foreach ($this->link_list as $value)
			{
				$output .= str_replace($rlink, array($value['attribute_value'], $value['content']), $this->link_tag_template);
			}
		}

		return $output;
	}

	/**
	 * Forget Meta Tag or Link Tag
	 * 
	 * @param  string $tag (one of : meta or link)
	 * @param  string $key key name
	 * @return boolean
	 */
	public function forget($tag = null, $key = 'all')
	{
		if (in_array($tag, ['meta','link']))
		{
			if (isset($key))
			{
				if ($key == 'all')
				{
					unset($this->{$tag.'_list'});
				}
				else
				{
					unset($this->{$tag.'_list'}[sha1($key)]);
				}
			}
		}
	}

	/**
	 * Validation Meta Tag
	 * 
	 * @param  string $attribute_key   attribute key
	 * @param  string $attribute_value attribute value
	 * @param  string $content         content value
	 * @return boolean
	 */
	private function run_validation($attribute_key = null, $attribute_value = null, $content = null)
	{
		$explode = explode(':', $attribute_value);

		if (!empty($explode))
		{
			switch ($explode[0])
			{
				case 'og':

					/**
					 * og : site_name
					 * og : title
					 * og : description
					 * og : url
					 */
					if (in_array($explode[1], ['site_name','title','description','url']))
					{
						$this->meta_list[sha1($attribute_key.$attribute_value)] =
						['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
					}

					/**
					 * og : type
					 */
					elseif ($explode[1] == 'type')
					{
						if ($this->vog_type_attribute($content) === TRUE)
						{
							$this->meta_list[sha1($attribute_key.$attribute_value)] =
							['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
						}
						else
						{
							$this->meta_list[sha1($attribute_key.$attribute_value)] =
							[
								'attribute_key'		=> $attribute_key,
								'attribute_value'	=> $attribute_value,
								'content'			=> $content,
								'error_message'		=> $this->vog_type_attribute($explode[1])
							];
						}
					}

					/**
					 * og : locale
					 */
					elseif ($explode[1] == 'locale')
					{
						if (isset($explode[2]))
						{
							if ($this->vog_locale_attribute($explode[2]) === TRUE)
							{
								$this->meta_list[sha1($attribute_key.$attribute_value)] 	=
								['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
							}
							else
							{
								$this->meta_list[sha1($attribute_key.$attribute_value)] =
								[
									'attribute_key'		=> $attribute_key,
									'attribute_value'	=> $attribute_value,
									'content'			=> $content,
									'error_message'		=> $this->vog_locale_attribute($explode[2])
								];
							}
						}
						else
						{
							$this->meta_list[sha1($attribute_key.$attribute_value)] =
							['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
						}
					}

					/*
						og : image
						og : image : secure_url
						...
					*/
					elseif ($explode[1] == 'image')
					{
						if (isset($explode[2]))
						{
							if ($this->vog_image_attribute($explode[2]) === TRUE)
							{
								$this->meta_list[sha1($attribute_key.$attribute_value.$content)] =
								['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
							}
							else
							{
								$this->meta_list[sha1($attribute_key.$attribute_value.$content)] =
								[
									'attribute_key'		=> $attribute_key,
									'attribute_value'	=> $attribute_value,
									'content'			=> $content,
									'error_message'		=> $this->vog_image_attribute($explode[2])
								];
							}
						}
						else
						{
							$this->meta_list[sha1($attribute_key.$attribute_value.$content)] =
							['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
						}
					}

					/*
						og : video
					*/
					elseif ($explode[1] == 'video')
					{
						if (isset($explode[2]))
						{
							if ($this->vog_video_attribute($explode[2]) === TRUE)
							{
								$this->meta_list[sha1($attribute_key.$attribute_value.$content)] =
								['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
							}
							else
							{
								$this->meta_list[sha1($attribute_key.$attribute_value.$content)] =
								[
									'attribute_key'		=> $attribute_key,
									'attribute_value'	=> $attribute_value,
									'content'			=> $content,
									'error_message'		=> $this->vog_video_attribute($explode[2])
								];
							}
						}
						else
						{
							$this->meta_list[sha1($attribute_key.$attribute_value.$content)] =
							['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
						}
					}
					else
					{
						$this->meta_list[sha1($attribute_key.$attribute_value)] =
						['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
					}

				break;

				/*
					place : location:latitude
					place : location:longitude
					place : ...
				*/
				case 'place':

					if (isset($explode[1]) && $explode[1] == 'location')
					{
						if (in_array($explode[2], ['latitude','longitude']))
						{
							$this->meta_list[sha1($attribute_key.$attribute_value)] 	=
							['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
						}
						else
						{
							$this->meta_list[sha1($attribute_key.$attribute_value)] =
							[
								'attribute_key'		=> $attribute_key,
								'attribute_value'	=> $attribute_value,
								'content'			=> $content,
								'error_message'		=> 'invalid location attribute'
							];
						}
					}

				break;

				/*
					profile : first_name
					profile : last_name
					profile : ...
				*/
				case 'profile':

					if (isset($explode[1]))
					{
						if ($this->vog_profile_attribute($explode[1]) === TRUE)
						{
							$this->meta_list[sha1($attribute_key.$attribute_value)] 	=
							['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
						}
						else
						{
							$this->meta_list[sha1($attribute_key.$attribute_value)] =
							[
								'attribute_key'		=> $attribute_key,
								'attribute_value'	=> $attribute_value,
								'content'			=> $content,
								'error_message'		=> $this->vog_profile_attribute($explode[1])
							];
						}
					}
					else
					{
						$this->meta_list[sha1($attribute_key.$attribute_value)] =
						['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
					}

				break;

				/*
					twitter : card
					twitter : ...
				*/
				case 'twitter':

					if (isset($explode[1]))
					{
						if ($this->vog_twitter_name($explode[1]) === TRUE)
						{
							if ($explode[1] == 'card')
							{
								if (in_array($content, ['summary','summary_large_image','app','player']))
								{
									$this->meta_list[sha1($attribute_key.$attribute_value)] 	=
									['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
								}
								else
								{
									$this->meta_list[sha1($attribute_key.$attribute_value)] =
									[
										'attribute_key'		=> $attribute_key,
										'attribute_value'	=> $attribute_value,
										'content'			=> $content,
										'error_message'		=> 'invalid card content'
									];
								}
							}
							else
							{
								$this->meta_list[sha1($attribute_key.$attribute_value)] 	=
								['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
							}
						}
						else
						{
							$this->meta_list[sha1($attribute_key.$attribute_value)] =
							[
								'attribute_key'		=> $attribute_key,
								'attribute_value'	=> $attribute_value,
								'content'			=> $content,
								'error_message'		=> $this->vog_twitter_name($explode[1])
							];
						}
					}

				break;

				/*
					article : author
					article : publisher
					article : ...
				*/
				case 'article':

					if (in_array($explode[1],['author','content_tier','expiration_time','modified_time','published_time','publisher','section','tag']))
					{
						if (in_array($explode[1], ['author','tag']))
						{
							$this->meta_list[sha1($attribute_key.$attribute_value.$content)] =
							['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
						}
						else
						{
							$this->meta_list[sha1($attribute_key.$attribute_value)] =
							['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];
						}
					}
					else
					{
						$this->meta_list[sha1($attribute_key)] =
						[
							'attribute_key'		=> $attribute_key,
							'attribute_value'	=> $attribute_value,
							'content'			=> $content,
							'error_message'		=> 'invalid article sub-attribute name'
						];
					}

				break;

				default:

					$this->meta_list[sha1($attribute_key.$attribute_value)] =
					['attribute_key' => $attribute_key,'attribute_value' => $attribute_value,'content' => $content];

				break;
			}
		}
	}

	/**
	 * Validate Meta Attribute
	 * 
	 * @param  string $attribute_key attribute key
	 * @return boolean
	 */
	private function validate_meta_attribute($attribute_key = null)
	{
		$valid_attributes =
		[
			'name',
			'property',
			'itemprop',
			'http-equiv'
		];

		return (!empty(trim($attribute_key)) && in_array($attribute_key, $valid_attributes))?TRUE:FALSE;
	}

	/**
	 * VOG prefix function is "Validation Open Graph"
	 */

	/* Open Graph Type */
	private function vog_type_attribute($attribute = null)
	{
		return (in_array($attribute, ['article','book','profile','website']))?TRUE:'invalid type attribute';
	}

	/* Open Graph Article */
	private function vog_article_attribute($attribute = null)
	{
		return (in_array($attribute, ['published_time','modified_time','expiration_time','author','section','tag']))?TRUE:'invalid article attribute';
	}

	/* Open Graph Book */
	private function vog_book_attribute($attribute = null)
	{
		return (in_array($attribute, ['author','isbn','release_date','tag']))?TRUE:'invalid book attribute';
	}

	/* Open Graph Locale */
	private function vog_locale_attribute($attribute = null)
	{
		return (in_array($attribute, ['alternate']))?TRUE:'invalid locale attribute';
	}

	/* Open Graph Profile */
	private function vog_profile_attribute($attribute = null)
	{
		return (in_array($attribute, ['first_name','last_name','username','gender']))?TRUE:'invalid profile attribute';
	}

	/* Open Graph Image */
	private function vog_image_attribute($attribute = null)
	{
		return (in_array($attribute, ['url','secure_url','type','width','height','alt']))?TRUE:'invalid image attribute';
	}

	/* Open Graph Video */
	private function vog_video_attribute($attribute = null)
	{
		return (in_array($attribute, ['secure_url','type','width','height']))?TRUE:'invalid video attribute';
	}

	/* Twitter */
	private function vog_twitter_name($attribute = null)
	{
		return (in_array($attribute, ['card','site','title','description','creator','domain']))?TRUE:'invalid twitter attribute';
	}
}

/* End of file SEO.php */
/* Location : ./application/libraries/Template_Engine/class/SEO.php */