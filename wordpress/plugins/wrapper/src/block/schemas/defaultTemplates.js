export const defaultTemplates = {
	unselected: {
		template: {},
		query: '',
	},
	PostCard: {
		template: {
			id: 'undefined',
			post_name: 'example-post',
			post_title: 'Example title',
			thumbnail: {},
		},
		query: `query
posts( 
$post_type:String!="post",
$limit:Int=12, 
$page:Int=0 ){ 
	nq:search(
		post_type:$post_type,
		limit:$limit, 
		skip:$page){ 
			id:ID 
			post_title 
			post_name 
			post_date
			post_excerpt
			thumbnail 
	}
}`,
	},
	CategoryCard: {
		template: { name: 'string', slug: 'string' },
		query: `query Categories($name: String = "product_cat")
{ query: categories(name: $name) {
    name
    slug
  }
}`,
	},
	ProductCard: {
		template: {
			id: 'undefined',
			post_name: 'example-product',
			post_title: 'Example title',
			thumbnail: {},
			categories: {
				product_cat: [ { name: 'string', slug: 'string' } ],
			},
			post_meta: {
				_thumbnail_id: {},
				_product_image_gallery: {},
				_sale_price: {},
			},
		},
		query:
`query posts(
	$post_type: String! = "product",
	$limit: Int = 12,
	$page: Int = 0,
	$term_slug: [String] = [])
{ nq:search( 
	post_type: $post_type, 
	limit: $limit, 
	skip: $page,
	terms: $term_slug
 )
	{
		id: ID
		post_title
		post_name
		categories
		thumbnail
		post_meta
		
	}
}`,
	},
	RelatedProducts: {
		template: {
			id: 'undefined',
			post_name: 'example-product',
			post_title: 'Example title',
			thumbnail: {},
			categories: {
				product_cat: [ { name: 'string', slug: 'string' } ],
			},
			post_meta: {
				_thumbnail_id: {},
				_product_image_gallery: {},
				_sale_price: {},
			},
		},
		query:
			`query posts($post_name: String!) {
  nq: relatedPosts(name: $post_name) {
    id:ID
    post_title
    post_content
    post_name
    categories
    thumbnail
    post_meta
  }
}`,
	},
	SinglePost: {
		template: {
			id: undefined,
			categories: {
				category: [ { name: 'string', slug: 'string' } ],
			},
			post_name: 'post_title',
			post_title: 'Post title',
			thumbnail: {},
		},
		query: `
query posts($post_name: String) {
  nq: queryPost(post_name: $post_name) {
    id:ID
    post_title
    post_content
    post_name
    categories
    thumbnail
  }
}`,
	},
	SingleProduct: {
		template: {
			id: undefined,
			categories: {
				product_cat: [ { name: 'string', slug: 'string' } ],
			},
			post_name: 'post_title',
			post_title: 'Post title',
			thumbnail: {},
			post_meta: {
				_product_image_gallery: {},
				_thumbnail_id: {},
				_product_attributes: {},
			},
		},
		query: `
query posts($post_name: String) {
  nq: queryPost(post_name: $post_name parent:0) {
    id:ID
    post_title
    post_content
    post_name
    categories
    thumbnail
    post_meta
  }
}`,
	},
	Search: {
		template: {
			id: undefined,
			categories: {
				product_cat: [ { name: 'string', slug: 'string' } ],
			},
			post_name: 'post_title',
			post_title: 'Post title',
			thumbnail: {},
			post_meta: {
				_product_image_gallery: {},
				_thumbnail_id: {},
			},
		},
		query: `query
					variables($post_name: String) {
					nq:search(query: $post_name){
						id:ID
						post_name
						post_title
						categories
						thumbnail
						post_meta
					}
				}`,
	},
};
