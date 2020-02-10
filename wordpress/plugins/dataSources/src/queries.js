export default {
	Posts: `query($limit: Int = 999, $skip: Int = 0, $terms: [String] = []) {
    posts(limit: $limit, skip: $skip, post_type: "post", terms: $terms) {
      post_title
      post_name
      post_content
      post_excerpt
			likes
			liked
      thumbnail {
        id
        src
        url
        sizes {
          size
          file
          url
          width
        }
      }
    }
  }`,
	Post: `query post($name: String! = "%self%") {
  post(name: $name) {
		likes
		liked
    id
    post_title
		post_content
    thumbnail {
      id
      src
      url
      sizes {
        size
        file
        url
        width
      }
    }
    post_meta(keys: [_price, _stock_status, _sale_price, _stock, _product_image_gallery]) {
      meta_key
      meta_value
    }
  }
}`,
	Products: `
		query posts($post_type: [String!]! = ["product"], $limit: Int = 999, $skip: Int = 0, $terms: [String] = []) {
			posts(post_type: $post_type, limit: $limit, skip: $skip, terms: $terms) {
				id
				post_type
				post_title
				post_name
				post_content
				likes
				liked
				categories(taxonomy: "product_cat"){
					name
					slug
				}
				thumbnail {
					id
					src
					url
					sizes {
						size
						file
						url
						width
					}
				}
				post_meta(keys: [_price, _stock_status, _sale_price, _stock, _product_image_gallery]) {
					meta_key
					meta_value
				}
			}
		}
  	`,
	'Product categories': `
		query Categories($name: String = "product_cat") {
			categories(name: $name) {
				type
				name
				slug
				thumbnail {
					url
					sizes {
						file
						url
						width
					}
				}
			}
		}
	`,
	'Related Product': `query Related($name: String = "%self%") {
  relatedPosts(name: $name) {
    	id
				post_type
				post_title
				post_name
				post_content
				likes
				liked
				categories(taxonomy: "product_cat"){
					name
					slug
				}
				thumbnail {
					id
					src
					url
					sizes {
						size
						file
						url
						width
					}
				}
				post_meta(keys: [_price, _stock_status, _sale_price, _stock, _product_image_gallery]) {
					meta_key
					meta_value
				}
  }
}`,
};
