#Migrate users table ignoring the 1st user since the seeder will add them
insert into db_gotta_news.users (id, first_name, last_name, email, username, display_name, created_at, updated_at, password)
select ID as id, display_name as first_name, display_name as last_name, user_email as email, user_email as username, display_name, user_registered as created_at, user_registered as updated_at, user_pass as password
from aklo_users
where ID > 1
order by ID ASC;

#Post Authors
insert into  db_gotta_news.post_authors (post_id, author_id)
select a.ID as post_id, a.post_author as author_id
from aklo_posts a
where a.post_status = 'publish' and a.post_type = 'post'
order by a.ID asc;

update posts set featured_image = (select a.featured_image from thumbnails a where a.ID = id);

#Insert posts including category_id and seo details from YOAST
insert into db_gotta_news.posts (id, title, slug, excerpt, in_summary, created_at, last_updated_at, published_at, body, seo_keywords, seo_title, seo_description, category_id)
select a.ID as id, a.post_title as title, a.post_name as slug, SUBSTRING(a.post_excerpt, 1, 255) as excerpt, a.post_excerpt as in_summary, a.post_date as created_at, a.post_modified as last_updated_at, a.post_modified as published_at, a.post_content as body,
(select meta_value from aklo_postmeta where post_id = a.ID and meta_key = '_wds_focus-keywords') as seo_keywords,
(select meta_value from aklo_postmeta where post_id = a.ID and meta_key = '_wds_title') as seo_title,
(select meta_value from aklo_postmeta where post_id = a.ID and meta_key = '_wds_metadesc') as seo_description,
(select term_taxonomy_id from aklo_term_relationships where object_id = a.ID and term_taxonomy_id in (select term_id from aklo_term_taxonomy where taxonomy = 'category')) as category_id
from aklo_posts a
where a.post_status = 'publish' and a.post_type = 'post'
limit 10;

#Publish all posts
update posts set status_id = 3 where id > 0;


#Insert into post tags
insert into  db_gotta_news.post_tags (post_id, tag_id)
select a.ID as post_id, b.term_taxonomy_id as tag_id
from aklo_posts a
join aklo_term_relationships b on (a.ID = b.object_id)
join aklo_term_taxonomy c on (c.term_id = b.term_taxonomy_id)
join aklo_terms d on (d.term_id = c.term_id)
where a.post_status = 'publish' and a.post_type = 'post' and c.taxonomy = 'post_tag'
order by a.ID asc
limit 10;

#Wordpress
ALTER TABLE `wpoc_posts` CHANGE `post_date` `post_date` datetime NOT NULL DEFAULT '2022-10-13 16:17:00' AFTER `post_author`,
DROP `post_date_gmt`, CHANGE `post_modified` `post_modified` datetime NOT NULL DEFAULT '2022-10-13 16:17:00' AFTER `pinged`,
DROP `post_modified_gmt`,
ADD `processed` varchar(1) NULL DEFAULT '0',
ADD `message` varchar(255) NULL AFTER `processed`;

set foreign_key_checks = 0;
truncate table post_tags;
truncate table post_authors;
truncate table posts;
update wpoc_posts set processed = 0 where ID > 0;
set foreign_key_checks = 1;

# $images = Image::orderBy('id', 'ASC')->skip(5500)->take(500)->get();
        # if ($images) {
        #     foreach ($images as $image) {
        #         $file = $image->file_name;
        #         echo $file . "</br>";
        #         $arry = explode("/", $file);
        #         $ext = explode(".", $arry[3]);

        #         $filepath = storage_path("app/public/{$file}");
        #         $target_path = storage_path("app/public/new_uploads/{$arry[1]}/{$arry[2]}/{$arry[3]}");
        #         try {
        #             copy($filepath, $target_path); #Store original

        #             foreach (et_thumbnail_sizes() as $key => $value) {
        #                 $thumbnail = $ext[0] . '_' . $key . '.' . $ext[1];
        #                 echo $thumbnail . "</br>";
        #                 $target_path = storage_path("app/public/new_uploads/{$arry[1]}/{$arry[2]}/{$thumbnail}");
        #                 copy($filepath, $target_path);

        #                 try {
        #                     $this->createThumbnail($target_path, $value, $value);
        #                 } catch (Exception $e) {
        #                     info($e->getMessage());
        #                 }
        #             }
        #         } catch (Exception $ex) {
        #             echo $image->id . ' - ' . $ex->getMessage();
        #         }
        #     }
        # }
        # die;

#https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=UC_zA9UIWE1fB-jfFk_DBSYw&maxResults=5&order=date&type=video&key=AIzaSyACREnYRZxc2ORXnfVEN_KLECLZ_e4yra0
