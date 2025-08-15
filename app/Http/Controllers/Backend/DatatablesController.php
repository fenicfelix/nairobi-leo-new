<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DatatablesController extends Controller
{
    public function get_users(Request $request): \Illuminate\Http\JsonResponse
    {
        $idx = 0;
        $columns = array(
            $idx++   =>  'id',
            $idx++   =>  'first_name',
            $idx++   =>  'phone_number',
            $idx++   =>  'email',
            $idx++   =>  'username',
            $idx++   =>  'role',
            $idx++   =>  'posts',
            $idx++   =>  'active',
        );

        $group_id = Auth::user()->group_id;
        if ($group_id) {
            $coulmns[$idx++] = 'id';
        }

        $limit = $request->get('length');
        $start = $request->get('start');
        $order = $request->get('order') ? $columns[$request->get('order')[0]['column']] : "a.id";
        $dir = $request->get('order') ? $request->get('order')[0]['dir'] : "desc";

        $sql_extras = "";
        $sql_count = "select count(distinct(a.id)) as total";
        $sql = "select a.id, a.thumbnail, a.display_name, a.first_name, a.last_name, a.phone_number, a.email, a.username, a.group_id, b.name as role, a.active,
        (select count(post_id) from post_authors where author_id = a.id) as posts";

        $sql_count_from = " from users a
        left join user_groups b on (a.group_id = b.id)";

        $sql_from = $sql_count_from;

        $sql_where = " where a.id > 0";

        $items_count = DB::select($sql_count . $sql_count_from . $sql_where);

        $sql_filter = "";
        if (!empty($request->get('search')['value'])) {
            $search = str_replace('"', '\"', $request->get('search')['value']);
            $sql_filter .= ' and (a.first_name like "%' . $search . '%" or a.last_name like "%' . $search . '%" or a.username like "%' . $search . '%" or a.email like "%' . $search . '%" or b.name like "%' . $search . '%")';
        }

        $filtered_count = DB::select($sql_count . $sql_count_from . $sql_where . $sql_filter);
        //$sql_extras .= " group by a.id";
        $sql_extras .= " order by $order $dir";
        if ($limit > -1) $sql_extras .= " limit $limit offset $start";
        $items_result = [];

        try {
            $items = DB::select($sql . $sql_from . $sql_where . $sql_filter . $sql_extras);
            $count = 1;
            if (!empty($items)) {
                $nestedValues = [];
                foreach ($items as $item) {
                    $idx = 0;
                    $nestedValues[$idx++] = $count++;
                    $thumbnail = "https://ui-avatars.com/api/?name=" . $item->first_name . "+" . $item->last_name . "&color=4c4e52&background=bdbec1";
                    if ($item->thumbnail) {
                        $thumbnail = Storage::disk('public')->url($item->thumbnail);
                    }
                    $html = '<div class="d-flex align-items-center gap-3">
                        <img src="' . $thumbnail . '" alt="" width="40" height="40" class="rounded-circle" loading="lazy">
                        <div class="d-flex flex-column">
                          <h6>' . $item->first_name . " " . $item->last_name . '</h6>
                          <small class="text-secondary">' . $item->display_name . '</small>
                        </div>
                      </div>';
                    $nestedValues[$idx++] = $html;
                    $nestedValues[$idx++] = $item->phone_number;
                    $nestedValues[$idx++] = $item->email;
                    $nestedValues[$idx++] = $item->username;
                    $nestedValues[$idx++] = $item->role;
                    $nestedValues[$idx++] = number_format($item->posts);
                    $nestedValues[$idx++] = ($item->active) ? '<i class="fas fa-circle text-success"></i>' : '<i class="fas fa-circle text-danger"></i>';
                    if ($group_id == 1) {
                        $task = '<a href="' . route("users.show", $item->username) . '" class="btn-show btn-action"><i class="fas fa-edit text-primary"></i></a>&nbsp;
                            <a href="#" class="btn-edit btn-action" data-id="' . $item->id . '" data-fname="' . $item->first_name . '" data-lname="' . $item->last_name . '" data-group="' . $item->group_id . '" data-phone="' . $item->phone_number . '" data-email="' . $item->email . '" data-username="' . $item->username . '"><i class="fas fa-eye text-default"></i></a>&nbsp;
                            <a href="#" class="btn-trash-user btn-action" data-id="' . $item->id . '" data-name="' . $item->display_name . '" data-bs-toggle="modal" data-bs-target="#deleteUserConfirmationModal"><i class="fas fa-trash text-danger"></i></a>';
                        $nestedValues[$idx++] = $task;
                    }
                    array_push($items_result, $nestedValues);
                }
            }
        } catch (Exception $e) {
            info("error", ["exception" => $e->getMessage()]);
        }

        $response = [
            "draw"              => intval($request->get('draw')),
            "recordsTotal"      => intval($items_count[0]->total),
            "recordsFiltered"   => intval($filtered_count[0]->total),
            "data" => $items_result ?? []
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    public function get_user_groups(Request $request): \Illuminate\Http\JsonResponse
    {
        $idx = 0;
        $columns = array(
            $idx++   =>  'id',
            $idx++   =>  'name',
            $idx++   =>  'description',
        );

        $group_id = Auth::user()->group_id;
        if ($group_id) {
            $coulmns[$idx++] = 'id';
        }

        $limit = $request->get('length');
        $start = $request->get('start');
        $order = $request->get('order') ? $columns[$request->get('order')[0]['column']] : "a.id";
        $dir = $request->get('order') ? $request->get('order')[0]['dir'] : "desc";

        $sql_extras = "";
        $sql_count = "select count(distinct(a.id)) as total";
        $sql = "select a.*";

        $sql_count_from = " from user_groups a";

        $sql_from = $sql_count_from;

        $sql_where = " where a.id > 0";

        $items_count = DB::select($sql_count . $sql_count_from . $sql_where);

        $sql_filter = "";
        if (!empty($request->get('search')['value'])) {
            $search = str_replace('"', '\"', $request->get('search')['value']);
            $sql_filter .= ' and (a.name like "%' . $search . '%" or a.description like "%' . $search . '%")';
        }

        $filtered_count = DB::select($sql_count . $sql_count_from . $sql_where . $sql_filter);
        //$sql_extras .= " group by a.id";
        $sql_extras .= " order by $order $dir";
        if ($limit > -1) $sql_extras .= " limit $limit offset $start";
        $items_result = [];

        try {
            $items = DB::select($sql . $sql_from . $sql_where . $sql_filter . $sql_extras);
            $count = 1;
            if (!empty($items)) {
                $nestedValues = [];
                foreach ($items as $item) {
                    $idx = 0;
                    $nestedValues[$idx++] = $count++;
                    $nestedValues[$idx++] = $item->name;
                    $nestedValues[$idx++] = $item->description;
                    if ($group_id == 1) {
                        $task = '<a href="#" class="btn-edit btn-action" data-id="' . $item->id . '" data-name="' . $item->name . '" data-desc="' . $item->description . '" ><i class="fas fa-edit text-primary"></i></a>';
                        $nestedValues[$idx++] = "<center>" . $task . "</center>";
                    }
                    array_push($items_result, $nestedValues);
                }
            }
        } catch (Exception $e) {
            info("error", ["exception" => $e->getMessage()]);
        }

        $response = [
            "draw"              => intval($request->get('draw')),
            "recordsTotal"      => intval($items_count[0]->total),
            "recordsFiltered"   => intval($filtered_count[0]->total),
            "data" => $items_result ?? []
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    public function get_shows(Request $request): \Illuminate\Http\JsonResponse
    {
        $idx = 0;
        $columns = array(
            $idx++   =>  'id',
            $idx++   =>  'id',
            $idx++   =>  'title',
            $idx++   =>  'synopsis',
            $idx++   =>  'created_at',
            $idx++   =>  'seo_status',
            $idx++   =>  'active',
            $idx++   =>  'id',
        );

        $limit = $request->get('length');
        $start = $request->get('start');
        $order = $request->get('order') ? $columns[$request->get('order')[0]['column']] : "a.id";
        $dir = $request->get('order') ? $request->get('order')[0]['dir'] : "desc";

        $sql_extras = "";
        $sql_count = "select count(distinct(a.id)) as total";
        $sql = "select a.*, b.file_name as banner_src";

        $sql_count_from = " from shows a
        left join images b on (a.banner_img = b.id)";

        $sql_from = $sql_count_from;

        $sql_where = " where a.id > 0";

        $items_count = DB::select($sql_count . $sql_count_from . $sql_where);

        $sql_filter = "";
        if (!empty($request->get('search')['value'])) {
            $search = str_replace('"', '\"', $request->get('search')['value']);
            $sql_filter .= ' and (a.title like "%' . $search . '%" or a.synopsis like "%' . $search . '%" or a.description like "%' . $search . '%" or b.title like "%' . $search . '%")';
        }

        $filtered_count = DB::select($sql_count . $sql_count_from . $sql_where . $sql_filter);
        //$sql_extras .= " group by a.id";
        $sql_extras .= " order by $order $dir";
        if ($limit > -1) $sql_extras .= " limit $limit offset $start";
        $items_result = [];

        try {
            $items = DB::select($sql . $sql_from . $sql_where . $sql_filter . $sql_extras);
            $count = 1;
            if (!empty($items)) {
                $nestedValues = [];
                foreach ($items as $item) {
                    $idx = 0;
                    $nestedValues[$idx++] = $count++;
                    $banner = "";
                    if ($item->banner_src) $banner = fetch_image($item->banner_src, "sm");
                    $nestedValues[$idx++] = '<img height="50" src="' . $banner . '" />';
                    $nestedValues[$idx++] = $item->title;
                    $nestedValues[$idx++] = $item->synopsis;
                    $nestedValues[$idx++] = date('d M, Y', strtotime($item->created_at));
                    $nestedValues[$idx++] = get_seo_status($item->seo_status);
                    $nestedValues[$idx++] = ($item->active) ? '<i class="fas fa-circle text-success"></i>' : '<i class="fas fa-circle text-danger"></i>';
                    $task = '<a href="' . route('tv.shows.edit', $item->id) . '" class="btn-edit btn-action"><i class="fas fa-edit text-primary"></i></a>&nbsp;
                            <a href="#" class="btn-trash btn-action" data-href="' . route('tv.shows.destroy', $item->id) . '" data-id="' . $item->id . '" data-name="' . $item->title . '" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"><i class="fas fa-trash text-danger"></i></a>';
                    $nestedValues[$idx++] = $task;
                    array_push($items_result, $nestedValues);
                }
            }
        } catch (Exception $e) {
            info("error", ["exception" => $e->getMessage()]);
        }

        $response = [
            "draw"              => intval($request->get('draw')),
            "recordsTotal"      => intval($items_count[0]->total),
            "recordsFiltered"   => intval($filtered_count[0]->total),
            "data" => $items_result ?? []
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    public function get_categories(Request $request): \Illuminate\Http\JsonResponse
    {
        $idx = 0;
        $columns = array(
            $idx++   =>  'id',
            $idx++   =>  'name',
            $idx++   =>  'slug',
            $idx++   =>  'parent_name',
            $idx++   =>  'seo_status',
            $idx++   =>  'posts',
            $idx++   =>  'active',
            $idx++   =>  'default',
            $idx++   =>  'id',
        );

        $limit = $request->get('length');
        $start = $request->get('start');
        $order = $request->get('order') ? $columns[$request->get('order')[0]['column']] : "a.id";
        $dir = $request->get('order') ? $request->get('order')[0]['dir'] : "desc";

        $sql_extras = "";
        $sql_count = "select count(distinct(a.id)) as total";
        $sql = "select a.*, b.slug as parent_slug, b.name as parent_name,
        (select count(id) from posts where category_id = a.id) as posts";

        $sql_count_from = " from categories a
        left join categories b on (a.parent = b.id)";

        $sql_from = $sql_count_from;

        $sql_where = " where a.id > 0";

        $items_count = DB::select($sql_count . $sql_count_from . $sql_where);

        $sql_filter = "";
        if (!empty($request->get('search')['value'])) {
            $search = str_replace('"', '\"', $request->get('search')['value']);
            $sql_filter .= ' and (a.name like "%' . $search . '%" or a.slug like "%' . $search . '%" or b.name like "%' . $search . '%")';
        }

        $filtered_count = DB::select($sql_count . $sql_count_from . $sql_where . $sql_filter);
        //$sql_extras .= " group by a.id";
        $sql_extras .= " order by $order $dir";
        if ($limit > -1) $sql_extras .= " limit $limit offset $start";
        $items_result = [];

        try {
            $items = DB::select($sql . $sql_from . $sql_where . $sql_filter . $sql_extras);
            $count = 1;
            if (!empty($items)) {
                $nestedValues = [];
                foreach ($items as $item) {
                    $idx = 0;
                    $nestedValues[$idx++] = $count++;
                    $nestedValues[$idx++] = ucwords(strtolower($item->name));
                    $nestedValues[$idx++] = $item->slug;
                    $nestedValues[$idx++] = $item->parent_name ?? "<center>-</center>";
                    $nestedValues[$idx++] = get_seo_status($item->seo_status);
                    $nestedValues[$idx++] = number_format($item->posts);
                    $nestedValues[$idx++] = ($item->active) ? '<i class="fas fa-circle text-success"></i>' : '<i class="fas fa-circle text-danger"></i>';
                    $nestedValues[$idx++] = ($item->default) ? '<i class="fas fa-circle text-success"></i>' : '<i class="fas fa-circle text-danger"></i>';
                    $task = '<a href="#" class="btn-edit btn-action seo-trigger" data-id="' . $item->id . '" data-name="' . $item->name . '" data-slug="' . $item->slug . '" data-parent="' . $item->parent . '" data-seo_keywords="' . $item->seo_keywords . '" data-seo_title="' . $item->seo_title . '" data-seo_description="' . $item->seo_description . '" data-seo_status="' . $item->seo_status . '"><i class="fas fa-edit text-primary"></i></a>&nbsp;';
                    if (!$item->default) $task .= '<a href="#" class="btn-trash btn-action" data-href="' . route('delete_category') . '" data-id="' . $item->id . '" data-name="' . $item->name . '" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal"><i class="fas fa-trash text-danger"></i></a>';
                    $nestedValues[$idx++] = $task;
                    array_push($items_result, $nestedValues);
                }
            }
        } catch (Exception $e) {
            info("error", ["exception" => $e->getMessage()]);
        }

        $response = [
            "draw"              => intval($request->get('draw')),
            "recordsTotal"      => intval($items_count[0]->total),
            "recordsFiltered"   => intval($filtered_count[0]->total),
            "data" => $items_result ?? []
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    public function get_tags(Request $request): \Illuminate\Http\JsonResponse
    {
        $idx = 0;
        $columns = array(
            $idx++   =>  'id',
            $idx++   =>  'name',
            $idx++   =>  'slug',
            $idx++   =>  'seo_status',
            $idx++   =>  'posts',
            $idx++   =>  'id',
            $idx++   =>  'id',
        );

        $limit = $request->get('length');
        $start = $request->get('start');
        $order = $request->get('order') ? $columns[$request->get('order')[0]['column']] : "a.id";
        $dir = $request->get('order') ? $request->get('order')[0]['dir'] : "desc";

        $sql_extras = "";
        $sql_count = "select count(distinct(a.id)) as total";
        $sql = "select a.*,
        (select count(id) from post_tags where tag_id = a.id) as posts";

        $sql_count_from = " from tags a";

        $sql_from = $sql_count_from;

        $sql_where = " where a.id > 0";

        $items_count = DB::select($sql_count . $sql_count_from . $sql_where);

        $sql_filter = "";
        if (!empty($request->get('search')['value'])) {
            $search = str_replace('"', '\"', $request->get('search')['value']);
            $sql_filter .= ' and (a.name like "%' . $search . '%" or a.slug like "%' . $search . '%")';
        }

        $filtered_count = DB::select($sql_count . $sql_count_from . $sql_where . $sql_filter);
        //$sql_extras .= " group by a.id";
        $sql_extras .= " order by $order $dir";
        if ($limit > -1) $sql_extras .= " limit $limit offset $start";
        $items_result = [];

        try {
            $items = DB::select($sql . $sql_from . $sql_where . $sql_filter . $sql_extras);
            $count = 1;
            if (!empty($items)) {
                $nestedValues = [];
                foreach ($items as $item) {
                    $idx = 0;
                    $nestedValues[$idx++] = $count++;
                    $nestedValues[$idx++] = $item->name;
                    $nestedValues[$idx++] = $item->slug;
                    $nestedValues[$idx++] = number_format($item->posts);
                    $nestedValues[$idx++] = get_seo_status($item->seo_status);
                    $task = '<a href="#" class="btn-edit btn-action seo-trigger" data-id="' . $item->id . '" data-name="' . $item->name . '" data-slug="' . $item->slug . '" data-seo_keywords="' . $item->seo_keywords . '" data-seo_title="' . $item->seo_title . '" data-seo_description="' . $item->seo_description . '" data-seo_status="' . $item->seo_status .
                        '"><i class="fas fa-edit text-primary"></i></a>&nbsp;
                             <a href="#" class="btn-trash btn-action" data-href="' . route('tags.destroy', $item->id) . '" data-id="' . $item->id . '" data-name="' . $item->name . '" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"><i class="fas fa-trash text-danger"></i></a>';
                    $nestedValues[$idx++] = $task;
                    array_push($items_result, $nestedValues);
                }
            }
        } catch (Exception $e) {
            info("error", ["exception" => $e->getMessage()]);
        }

        $response = [
            "draw"              => intval($request->get('draw')),
            "recordsTotal"      => intval($items_count[0]->total),
            "recordsFiltered"   => intval($filtered_count[0]->total),
            "data" => $items_result ?? []
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    public function get_posts(Request $request, $type): \Illuminate\Http\JsonResponse
    {
        $idx = 0;
        $columns = array(
            $idx++   =>  'id',
            $idx++   =>  'title',
            $idx++   =>  'author',
            $idx++   =>  'category',
            $idx++   =>  'status',
            $idx++   =>  'published_at',
            $idx++   =>  'seo_status',
            $idx++   =>  'total_views',
            $idx++   =>  'id',
        );

        $limit = $request->get('length');
        $start = $request->get('start');
        $order = $request->get('order') ? $columns[$request->get('order')[0]['column']] : "a.id";
        $dir = $request->get('order') ? $request->get('order')[0]['dir'] : "desc";

        $sql_extras = "";
        $sql_count = "select count(distinct(a.id)) as total";
        $sql = "select a.*, b.name as category, c.display_name as author, d.name as status";

        $sql_count_from = " from posts a
        left join categories b on (a.category_id = b.id)
        left join users c on (a.created_by = c.id)
        left join statuses d on (a.status_id = d.id)";

        $sql_from = $sql_count_from;

        $sql_where = " where a.id > 0";

        if ($this->data["logged_user"]->group_id > 2) {
            $sql_where .= " and (a.created_by = '" . $this->data["logged_user"]->id . "' or a.published_by = '" . $this->data["logged_user"]->id . "' or a.id in (select post_id from post_authors where author_id = '" . $this->data["logged_user"]->id . "'))";
        }

        if ($type == "drafts") $sql_where .= " and a.status_id = 1";
        if ($type == "scheduled") $sql_where .= " and a.status_id = 2";
        if ($type == "published") $sql_where .= " and a.status_id = 3";
        if ($type == "trashed") $sql_where .= " and a.status_id = 4";

        $items_count = DB::select($sql_count . $sql_count_from . $sql_where);

        $sql_filter = "";
        if (!empty($request->get('search')['value'])) {
            $search = str_replace('"', '\"', $request->get('search')['value']);
            $sql_filter .= ' and (a.title like "%' . $search . '%" or a.body like "%' . $search . '%" or c.display_name like "%' . $search . '%" or d.name like "%' . $search . '%" or b.name like "%' . $search . '%")';
        }

        $filtered_count = DB::select($sql_count . $sql_count_from . $sql_where . $sql_filter);
        //$sql_extras .= " group by a.id";
        $sql_extras .= " order by $order $dir";
        if ($limit > -1) $sql_extras .= " limit $limit offset $start";
        $items_result = [];

        try {
            $items = DB::select($sql . $sql_from . $sql_where . $sql_filter . $sql_extras);
            $count = 1;
            if (!empty($items)) {
                $nestedValues = [];
                foreach ($items as $item) {
                    $idx = 0;
                    $nestedValues[$idx++] = $count++;
                    $nestedValues[$idx++] = $item->title;
                    $nestedValues[$idx++] = $item->author;
                    $nestedValues[$idx++] = $item->category;
                    $nestedValues[$idx++] = $item->status;
                    $nestedValues[$idx++] = ($item->published_at) ? date('d M, Y h:i A', strtotime($item->published_at)) : '-';
                    $nestedValues[$idx++] = get_seo_status($item->seo_status);
                    $nestedValues[$idx++] = number_format($item->total_views);
                    $task = '<a href="' . route('preview', $item->id) . '" target="_blank" class="btn-action"><i class="fas fa-eye text-default"></i></a>';
                    if ($item->status_id != 4) {
                        if (in_array($this->data["logged_user"]->group_id, [1, 2]) || (!in_array($this->data["logged_user"]->group_id, [1, 2]) && $item->status_id != 3)) {
                            $task .= '<a href="' . route('posts.edit', $item->id) . '" class="btn-edit btn-action"><i class="fas fa-edit text-primary"></i></a>&nbsp;';
                            $task .= '<a href="#" class="btn-trash btn-action" data-href="' . route('posts.destroy', $item->id) . '" data-id="' . $item->id . '" data-name="' . $item->title . '" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"><i class="fas fa-trash text-danger"></i></a>';
                        }
                    } else {
                        $task .= '<a href="#" class="btn-recover btn-action" data-href="' . route('post.recover', $item->id) . '" data-id="' . $item->id . '" data-name="' . $item->title . '" data-bs-toggle="modal" data-bs-target="#recoverConfirmationModal"><i class="fas fa-sync-alt text-success"></i></a>';
                        if ($this->data["logged_user"]->group_id == 1) $task .= '<a href="#" class="btn-delete-permanent btn-action" data-href="' . route('post.delete_permanently', $item->id) . '" data-id="' . $item->id . '" data-name="' . $item->title . '" data-bs-toggle="modal" data-bs-target="#permanentDeleteConfirmationModal"><i class="fas fa-trash text-danger"></i></a>';
                    }
                    $nestedValues[$idx++] = $task;
                    array_push($items_result, $nestedValues);
                }
            }
        } catch (Exception $e) {
            info("error", ["exception" => $e->getMessage()]);
        }

        $response = [
            "draw"              => intval($request->get('draw')),
            "recordsTotal"      => intval($items_count[0]->total),
            "recordsFiltered"   => intval($filtered_count[0]->total),
            "data" => $items_result ?? []
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    public function get_pages(Request $request)
    {
        $idx = 0;
        $columns = array(
            $idx++   =>  'id',
            $idx++   =>  'title',
            $idx++   =>  'slug',
            $idx++   =>  'category_name',
            $idx++   =>  'template',
            $idx++   =>  'seo_status',
        );

        if (in_array($this->data["logged_user"]->group_id, [1, 2])) {
            $columns[$idx++] = "id";
        }

        $limit = $request->get('length');
        $start = $request->get('start');
        $order = $request->get('order') ? $columns[$request->get('order')[0]['column']] : "a.id";
        $dir = $request->get('order') ? $request->get('order')[0]['dir'] : "desc";

        $sql_extras = "";
        $sql_count = "select count(distinct(a.id)) as total";
        $sql = "select a.*, b.name as category_name";

        $sql_count_from = " from pages a
        left join categories b on (a.category_id = b.id)";

        $sql_from = $sql_count_from;

        $sql_where = " where a.id > 0";

        $items_count = DB::select($sql_count . $sql_count_from . $sql_where);

        $sql_filter = "";
        if (!empty($request->get('search')['value'])) {
            $search = str_replace('"', '\"', $request->get('search')['value']);
            $sql_filter .= ' and (a.title like "%' . $search . '%" or b.name like "%' . $search . '%")';
        }



        $filtered_count = DB::select($sql_count . $sql_count_from . $sql_where . $sql_filter);
        //$sql_extras .= " group by a.id";
        $sql_extras .= " order by $order $dir";
        if ($limit > -1) $sql_extras .= " limit $limit offset $start";
        $items_result = [];

        try {
            $items = DB::select($sql . $sql_from . $sql_where . $sql_filter . $sql_extras);
            $count = 1;
            if (!empty($items)) {
                $nestedValues = [];
                foreach ($items as $item) {
                    $idx = 0;
                    $nestedValues[$idx++] = $count++;
                    $nestedValues[$idx++] = $item->title;
                    $nestedValues[$idx++] = $item->slug;
                    $nestedValues[$idx++] = $item->category_name;
                    $nestedValues[$idx++] = $item->template;
                    $nestedValues[$idx++] = get_seo_status($item->seo_status);
                    $task = "";
                    if (in_array($this->data["logged_user"]->group_id, [1, 2])) {
                        $task .= '<a href="' . route('pages.edit', $item->id) . '" class="btn-edit btn-action"><i class="fas fa-edit text-primary"></i></a>&nbsp;';
                    }
                    if ($this->data["logged_user"]->group_id == 1) {
                        $task .= '<a href="#" class="btn-trash btn-action" data-href="' . route('tags.destroy', $item->id) . '" data-id="' . $item->id . '" data-name="' . $item->title . '" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"><i class="fas fa-trash text-danger"></i></a>';
                    }
                    if ($task != "") $nestedValues[$idx++] = $task;
                    array_push($items_result, $nestedValues);
                }
            }
        } catch (Exception $e) {
            info("error", ["exception" => $e->getMessage()]);
        }

        $response = [
            "draw"              => intval($request->get('draw')),
            "recordsTotal"      => intval($items_count[0]->total),
            "recordsFiltered"   => intval($filtered_count[0]->total),
            "data" => $items_result ?? []
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    public function get_videos(Request $request)
    {
        $idx = 0;
        $columns = array(
            $idx++   =>  'id',
            $idx++   =>  'thumbnail_sm',
            $idx++   =>  'title',
            $idx++   =>  'video_id',
            $idx++   =>  'source',
            $idx++   =>  'live',
            $idx++   =>  'published',
            $idx++   =>  'id',
        );

        if (in_array($this->data["logged_user"]->group_id, [1, 2])) {
            $columns[$idx++] = "id";
        }

        $limit = $request->get('length');
        $start = $request->get('start');
        $order = $request->get('order') ? $columns[$request->get('order')[0]['column']] : "a.id";
        $dir = $request->get('order') ? $request->get('order')[0]['dir'] : "desc";

        $sql_extras = "";
        $sql_count = "select count(distinct(a.id)) as total";
        $sql = "select a.*";

        $sql_count_from = " from videos a";

        $sql_from = $sql_count_from;

        $sql_where = " where a.published = 1";

        $items_count = DB::select($sql_count . $sql_count_from . $sql_where);

        $sql_filter = "";
        if (!empty($request->get('search')['value'])) {
            $search = str_replace('"', '\"', $request->get('search')['value']);
            $sql_filter .= ' and (a.title like "%' . $search . '%" or a.video_id like "%' . $search . '%" or a.description like "%' . $search . '%")';
        }



        $filtered_count = DB::select($sql_count . $sql_count_from . $sql_where . $sql_filter);
        //$sql_extras .= " group by a.id";
        $sql_extras .= " order by $order $dir";
        if ($limit > -1) $sql_extras .= " limit $limit offset $start";
        $items_result = [];

        try {
            $items = DB::select($sql . $sql_from . $sql_where . $sql_filter . $sql_extras);
            $count = 1;
            if (!empty($items)) {
                $nestedValues = [];
                foreach ($items as $item) {
                    $idx = 0;
                    $nestedValues[$idx++] = $count++;
                    $nestedValues[$idx++] = '<img src="' . $item->thumbnail_sm . '" width="50"/>';
                    $nestedValues[$idx++] = $item->title;
                    $nestedValues[$idx++] = '<a target="blank" href="' . route('tv_live', $item->video_id) . '">' . $item->video_id . '</a>';
                    $nestedValues[$idx++] = $item->source;
                    $nestedValues[$idx++] = ($item->live) ? '<i class="fas fa-circle text-success"></i>' : '<i class="fas fa-circle text-danger"></i>';
                    $nestedValues[$idx++] = ($item->published) ? '<i class="fas fa-circle text-success"></i>' : '<i class="fas fa-circle text-danger"></i>';
                    $task = "";
                    $task .= '<a href="#" class="btn-trash btn-action" data-href="' . route('videos.destroy', $item->id) . '" data-id="' . $item->id . '" data-name="' . $item->title . '" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"><i class="fas fa-trash text-danger"></i></a>';
                    $nestedValues[$idx++] = $task;
                    array_push($items_result, $nestedValues);
                }
            }
        } catch (Exception $e) {
            info("error", ["exception" => $e->getMessage()]);
        }

        $response = [
            "draw"              => intval($request->get('draw')),
            "recordsTotal"      => intval($items_count[0]->total),
            "recordsFiltered"   => intval($filtered_count[0]->total),
            "data" => $items_result ?? []
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    public function get_settings(Request $request)
    {
        $idx = 0;
        $columns = array(
            $idx++   =>  'id',
            $idx++   =>  'title',
            $idx++   =>  'slug',
            $idx++   =>  'value',
            $idx++   =>  'id',
        );

        $limit = $request->get('length');
        $start = $request->get('start');
        $order = $request->get('order') ? $columns[$request->get('order')[0]['column']] : "a.id";
        $dir = $request->get('order') ? $request->get('order')[0]['dir'] : "desc";

        $sql_extras = "";
        $sql_count = "select count(distinct(a.id)) as total";
        $sql = "select a.*";

        $sql_count_from = " from system_preferences a";

        $sql_from = $sql_count_from;

        $sql_where = " where a.id > 0";

        $items_count = DB::select($sql_count . $sql_count_from . $sql_where);

        $sql_filter = "";
        if (!empty($request->get('search')['value'])) {
            $search = str_replace('"', '\"', $request->get('search')['value']);
            $sql_filter .= ' and (a.title like "%' . $search . '%" or a.value like "%' . $search . '%")';
        }



        $filtered_count = DB::select($sql_count . $sql_count_from . $sql_where . $sql_filter);
        //$sql_extras .= " group by a.id";
        $sql_extras .= " order by $order $dir";
        if ($limit > -1) $sql_extras .= " limit $limit offset $start";
        $items_result = [];

        try {
            $items = DB::select($sql . $sql_from . $sql_where . $sql_filter . $sql_extras);
            $count = 1;
            if (!empty($items)) {
                $nestedValues = [];
                foreach ($items as $item) {
                    $idx = 0;
                    $nestedValues[$idx++] = $count++;
                    $nestedValues[$idx++] = $item->title;
                    $nestedValues[$idx++] = $item->slug;
                    $nestedValues[$idx++] = $item->value;
                    $task = "";
                    $task .= '<a href="#" class="btn-edit btn-action" data-id="' . $item->id . '" data-title="' . $item->title . '" data-slug="' . $item->slug . '" data-value="' . htmlspecialchars($item->value, ENT_QUOTES) . '" ><i class="fas fa-edit text-primary"></i></a>&nbsp;';
                    $task .= '<a href="#" class="btn-trash btn-action" data-href="' . route('delete_setting', $item->id) . '" data-id="' . $item->id . '" data-name="' . $item->title . '" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"><i class="fas fa-trash text-danger"></i></a>';
                    $nestedValues[$idx++] = $task;
                    array_push($items_result, $nestedValues);
                }
            }
        } catch (Exception $e) {
            info("error", ["exception" => $e->getMessage()]);
        }

        $response = [
            "draw"              => intval($request->get('draw')),
            "recordsTotal"      => intval($items_count[0]->total),
            "recordsFiltered"   => intval($filtered_count[0]->total),
            "data" => $items_result ?? []
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    public function get_widgets(Request $request)
    {
        $idx = 0;
        $columns = array(
            $idx++   =>  'id',
            $idx++   =>  'title',
            $idx++   =>  'slug',
            $idx++   =>  'last_updated_at',
            $idx++   =>  'id',
        );

        $limit = $request->get('length');
        $start = $request->get('start');
        $order = $request->get('order') ? $columns[$request->get('order')[0]['column']] : "a.id";
        $dir = $request->get('order') ? $request->get('order')[0]['dir'] : "desc";

        $sql_extras = "";
        $sql_count = "select count(distinct(a.id)) as total";
        $sql = "select a.*";

        $sql_count_from = " from widgets a";

        $sql_from = $sql_count_from;

        $sql_where = " where a.id > 0";

        $items_count = DB::select($sql_count . $sql_count_from . $sql_where);

        $sql_filter = "";
        if (!empty($request->get('search')['value'])) {
            $search = str_replace('"', '\"', $request->get('search')['value']);
            $sql_filter .= ' and (a.title like "%' . $search . '%" or a.body like "%' . $search . '%")';
        }



        $filtered_count = DB::select($sql_count . $sql_count_from . $sql_where . $sql_filter);
        //$sql_extras .= " group by a.id";
        $sql_extras .= " order by $order $dir";
        if ($limit > -1) $sql_extras .= " limit $limit offset $start";
        $items_result = [];

        try {
            $items = DB::select($sql . $sql_from . $sql_where . $sql_filter . $sql_extras);
            $count = 1;
            if (!empty($items)) {
                $nestedValues = [];
                foreach ($items as $item) {
                    $idx = 0;
                    $nestedValues[$idx++] = $count++;
                    $nestedValues[$idx++] = $item->title;
                    $nestedValues[$idx++] = $item->slug;
                    $nestedValues[$idx++] = $item->last_updated_at;
                    $task = "";
                    $task .= '<a href="' . route('settings.widgets.edit', $item->id) . '" class="btn-edit btn-action" data-id="' . $item->id . '" data-title="' . $item->title . '"><i class="fas fa-edit text-primary"></i></a>&nbsp;';
                    $task .= '<a href="#" class="btn-trash btn-action" data-href="' . route('settings.widgets.destroy', $item->id) . '" data-id="' . $item->id . '" data-name="' . $item->title . '" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"><i class="fas fa-trash text-danger"></i></a>';
                    $nestedValues[$idx++] = $task;
                    array_push($items_result, $nestedValues);
                }
            }
        } catch (Exception $e) {
            info("error", ["exception" => $e->getMessage()]);
        }

        $response = [
            "draw"              => intval($request->get('draw')),
            "recordsTotal"      => intval($items_count[0]->total),
            "recordsFiltered"   => intval($filtered_count[0]->total),
            "data" => $items_result ?? []
        ];
        return response()->json($response, Response::HTTP_OK);
    }


    // ------------------------------------------------ eCommerce --------------------------------------------------

    public function get_products(Request $request): \Illuminate\Http\JsonResponse
    {
        $idx = 0;
        $columns = array(
            $idx++   =>  'id',
            $idx++   =>  'title',
            $idx++   =>  'category',
            $idx++   =>  'status',
            $idx++   =>  'published_at',
            $idx++   =>  'seo_status',
            $idx++   =>  'total_views',
            $idx++   =>  'unit_price',
            $idx++   =>  'unit_measurement',
            $idx++   =>  'id',
        );

        $limit = $request->get('length');
        $start = $request->get('start');
        $order = $request->get('order') ? $columns[$request->get('order')[0]['column']] : "a.id";
        $dir = $request->get('order') ? $request->get('order')[0]['dir'] : "desc";

        $sql_extras = "";
        $sql_count = "select count(distinct(a.id)) as total";
        $sql = "select b.*, a.id as product_id, a.sku, a.quantity, a.unit_price, a.unit_measurement, c.name as category, d.display_name as author, e.name as status";

        $sql_count_from = " from ecommerce_products a
        join posts b on (a.post_id = b.id)
        left join categories c on (b.category_id = c.id)
        left join users d on (b.created_by = d.id)
        left join statuses e on (b.status_id = e.id)";

        $sql_from = $sql_count_from;

        $sql_where = " where a.id > 0";

        if ($this->data["logged_user"]->group_id > 2) {
            $sql_where .= " and (b.created_by = '" . $this->data["logged_user"]->id . "' or b.published_by = '" . $this->data["logged_user"]->id . "' or b.id in (select post_id from post_authors where author_id = '" . $this->data["logged_user"]->id . "'))";
        }

        info("SQL: " . $sql . $sql_count_from . $sql_where);

        $items_count = DB::select($sql_count . $sql_count_from . $sql_where);

        $sql_filter = "";
        if (!empty($request->get('search')['value'])) {
            $search = str_replace('"', '\"', $request->get('search')['value']);
            $sql_filter .= ' and (b.title like "%' . $search . '%" or b.body like "%' . $search . '%" or d.display_name like "%' . $search . '%" or e.name like "%' . $search . '%" or c.name like "%' . $search . '%")';
        }

        $filtered_count = DB::select($sql_count . $sql_count_from . $sql_where . $sql_filter);
        //$sql_extras .= " group by a.id";
        $sql_extras .= " order by $order $dir";
        if ($limit > -1) $sql_extras .= " limit $limit offset $start";
        $items_result = [];

        try {
            $items = DB::select($sql . $sql_from . $sql_where . $sql_filter . $sql_extras);
            $count = 1;
            if (!empty($items)) {
                $nestedValues = [];
                foreach ($items as $item) {
                    $idx = 0;

                    $nestedValues[$idx++] = $count++;
                    $nestedValues[$idx++] = $item->title;
                    $nestedValues[$idx++] = $item->category;
                    $nestedValues[$idx++] = $item->status;
                    $nestedValues[$idx++] = ($item->published_at) ? date('d M, Y h:i A', strtotime($item->published_at)) : '-';
                    $nestedValues[$idx++] = get_seo_status($item->seo_status);
                    $nestedValues[$idx++] = $item->unit_price;
                    $nestedValues[$idx++] = $item->unit_measurement;
                    $task = '<a href="' . route('preview', $item->id) . '" target="_blank" class="btn-action"><i class="fas fa-eye text-default"></i></a>';
                    if ($item->status_id != 4) {
                        if (in_array($this->data["logged_user"]->group_id, [1, 2]) || (!in_array($this->data["logged_user"]->group_id, [1, 2]) && $item->status_id != 3)) {
                            $task .= '<a href="' . route('products.edit', $item->id) . '" class="btn-edit btn-action"><i class="fas fa-edit text-primary"></i></a>&nbsp;';
                            $task .= '<a href="#" class="btn-trash btn-action" data-href="' . route('posts.destroy', $item->id) . '" data-id="' . $item->id . '" data-name="' . $item->title . '" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"><i class="fas fa-trash text-danger"></i></a>';
                        }
                    } else {
                        $task .= '<a href="#" class="btn-recover btn-action" data-href="' . route('post.recover', $item->id) . '" data-id="' . $item->id . '" data-name="' . $item->title . '" data-bs-toggle="modal" data-bs-target="#recoverConfirmationModal"><i class="fas fa-sync-alt text-success"></i></a>';
                        if ($this->data["logged_user"]->group_id == 1) $task .= '<a href="#" class="btn-delete-permanent btn-action" data-href="' . route('post.delete_permanently', $item->id) . '" data-id="' . $item->id . '" data-name="' . $item->title . '" data-bs-toggle="modal" data-bs-target="#permanentDeleteConfirmationModal"><i class="fas fa-trash text-danger"></i></a>';
                    }
                    $nestedValues[$idx++] = $task;
                    array_push($items_result, $nestedValues);
                }
            }
        } catch (Exception $e) {
            info("error", ["exception" => $e->getMessage()]);
        }

        $response = [
            "draw"              => intval($request->get('draw')),
            "recordsTotal"      => intval($items_count[0]->total),
            "recordsFiltered"   => intval($filtered_count[0]->total),
            "data" => $items_result ?? []
        ];
        return response()->json($response, Response::HTTP_OK);
    }
}
