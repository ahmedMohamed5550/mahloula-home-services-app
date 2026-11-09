# Module: 06 - Projects & Community

Status: NOT STARTED  
Approval: PENDING  
Owner: Claude Code

---

## 1. Module purpose

Powers the community showcase and project request feed:
- Users publish projects tagged with a `Section`, `Service`, `city`, description, and media attachments (`img`, `video`).
- Role-based feed filtering:
  - Normal users see their own created projects.
  - Providers (employees and companies) see projects matching their assigned Section and Service.
- Community engagement: liking projects (`ProjectLike`), reading and posting comments (`Comment`), editing comments (marking `is_edited = 1`), and deleting comments.
- View count tracking (incremented upon `show`).
- Author authorization checks for projects and comments.
- Admin moderation in web dashboard (viewing all projects and deleting flagged projects or comments).

---

## 2. Scope and dependency inventory

| Type | Files / classes | Responsibility |
|---|---|---|
| Models | `App\Models\Project`, `App\Models\ProjectLike`, `App\Models\Comment` | Project posts, project likes, and comments |
| Routes | `routes/api.php`: `/projects/*`<br>`routes/web.php`: `admin/project/all`, `admin/prject/{id}`, `admin/prject/comment/{id}` | Community API endpoints and Admin web moderation routes |
| Controllers | `App\Http\Controllers\ProjectController`, `App\Http\Controllers\admin\ProjectController` | Post management, like/comment handling, feed filtering, moderation |
| Form Requests | `App\Http\Requests\CreateProjectRequest`, `App\Http\Requests\UpdateProjectRequest`, `App\Http\Requests\CommentRequest` | Input validation |
| Resources | `App\Http\Resources\ProjectResource`, `App\Http\Resources\ProjectLikesResource`, `App\Http\Resources\CommentResource` | Response shaping |
| Livewire components | `App\Livewire\SearchProject` | Admin panel project search table |
| Blade views | `resources/views/admin/project/*`, `resources/views/livewire/search-project.blade.php` | Moderation views and Livewire templates |
| Tests | None | Critical coverage gap |

---

## 3. Existing behavior contract

- **Project Feed (`GET /api/projects/index`)**:
  - Middleware: `auth:api`.
  - Pagination params: `per_page`, `sortBy` (default `id`), `sort` (default `desc`).
  - Filtering logic:
    - If `authUser->userType === 'user'`: filters `user_id = $authUser->id`.
    - If `authUser->userType in ('employee', 'company')`: filters `section_id = $authUser->employee->section_id` and `service_id = $authUser->employee->service_id`.
  - Determines `is_liked` boolean for current user on each project.
  - Returns HTTP 200 with `ProjectResource::collection($projects)->response()->getData(true)`. If empty, returns HTTP 404.
- **Project Detail (`GET /api/projects/show/{id}`)**:
  - Middleware: `auth:api`.
  - Increments `views` by 1 (`$project->increment('views')`).
  - Eager loads `service`, `user`, `section`, `comments`.
  - Determines `is_liked` boolean.
  - Returns HTTP 200 with `ProjectResource`.
- **Create Project (`POST /api/projects/create`)**:
  - Middleware: `auth:api`.
  - Validates `section_id`, `service_id`, `user_id`, `desc`, `city`, and optional `img` and `video`.
  - Saves media in `public/projects/images` and `public/projects/videos`, saves absolute `asset(...)` URLs in DB.
  - Returns HTTP 201 with `ProjectResource`.
- **Update Project (`POST /api/projects/update/{id}`)**:
  - Verifies `$authUser->id === $project->user_id`. If not, returns HTTP 401 `Not authorized`.
  - If new media provided, unlinks old file and updates URL.
  - Returns HTTP 200 with `ProjectResource`.
- **Delete Project (`DELETE /api/projects/destroy/{id}`)**:
  - Verifies `$authUser->id === $project->user_id`. If not, returns HTTP 401.
  - Deletes files, deletes related comments (`Comment::where('project_id', $projectId)->delete()`), deletes project.
  - Returns HTTP 200 `Project deleted successfully`.
- **Toggle Project Like (`POST /api/projects/{projectId}/like/{userId}`)**:
  - If like exists: deletes like, returns HTTP 200 `User unliked the project`.
  - If like does not exist: creates like, returns HTTP 200 `Project liked successfully`.
- **Comment CRUD**:
  - `Create`: `POST /api/projects/{project_id}/comment/{user_id}` validates `content`, returns HTTP 200 with `CommentResource`.
  - `Update`: `POST /api/projects/update/{project_id}/comment/{comment_id}` validates user ownership, updates content, sets `is_edited = 1`, returns HTTP 200 with `CommentResource`.
  - `Delete`: `DELETE /api/projects/delete/{project_id}/comment/{comment_id}` verifies user ownership, deletes comment, returns HTTP 200 `Comment deleted successfully`.
- **Web Admin Moderation**:
  - Routes in `web.php` use typo `/admin/prject/{id}` and `/admin/prject/comment/{id}`. Allows admin deletion without permission checks.

---

## 4. Relationships and dependencies

- **Internal dependencies**: `Project` has many `ProjectLike` and many `Comment`.
- **Dependencies on other modules**:
  - Module 01 (Author user).
  - Module 03 (Section and Service tags).
  - Module 04 (Employee section/service filtering context).
- **Safe implementation order**: Step 6 (After Modules 01, 03, 04).

---

## 5. Problems found

- **Query / Performance Risk — SEVERE N+1 QUERIES**:
  - In `ProjectController::index`:
    ```php
    $projects->getCollection()->each(function ($project) use ($authUser) {
        $project->is_liked = ProjectLike::where('user_id', $authUser->id)
            ->where('project_id', $project->id)
            ->exists(); // 1 query PER PROJECT
    });
    ```
  - Inside `ProjectResource` (`toArray`):
    ```php
    'likes'          => $this->likes()->count(),       // 1 COUNT query PER PROJECT
    'total_comments' => $this->comments()->count(),    // 1 COUNT query PER PROJECT
    ```
    For a page of 20 projects, this executes:
    $1 \text{ (base)} + 1 \text{ (count)} + 3 \times 20 \text{ (each loop + resource counts)} = \mathbf{62\text{ database queries}}!
- **Correctness & Consistency**:
  - Route typos in `web.php`: `admin/prject/{id}` and `admin/prject/comment/{id}`.
  - In `ProjectController::createComment`: URL contains `{user_id}`, but authenticated user's ID should be used; arbitrary user ID could be passed in URL.
  - In `ProjectController::update`: Does not set `is_edited = 1` for project post edits (even though `projects` table has `is_edited` column).
- **Security / Authorization**:
  - In `ProjectController::getTotalLikes` and `showComment`, anyone can view likers and comments without verifying project visibility.
- **Testing gap**: Zero automated tests for project feed, likes, comments, or moderation.

---

## 6. Proposed target structure

| Concern | Current location | Proposed location | Reason |
|---|---|---|---|
| HTTP validation | Form Requests | Keep & refine Form Requests | Request validation |
| Business orchestration | `ProjectController`, `admin\ProjectController` | `App\Services\Project\ProjectService`, `App\Services\Project\CommentService` | Isolate feed filtering, media upload, and comment lifecycle |
| Response formatting | `ProjectResource`, `CommentResource`, `ProjectLikesResource` | Retain exact Resources | Preserve JSON output contract |
| Query optimization | N+1 in controller + resource | `withCount(['likes', 'comments'])` + eager loading | Reduce 60+ queries to 4 queries |

---

## 7. Test plan

- **Characterization tests**:
  - `test_user_feed_shows_only_their_projects()`
  - `test_provider_feed_shows_matching_section_and_service_projects()`
  - `test_create_project_with_image_and_video()`
  - `test_update_project_requires_author_ownership()`
  - `test_project_show_increments_views()`
  - `test_toggle_project_like()`
  - `test_create_update_and_delete_comment()`
  - `test_comment_update_sets_is_edited_flag()`
- **Commands**: `php artisan test --filter ProjectTest`

---

## 8. Query optimization plan

- **Current query in `ProjectController::index`**:
  $60+$ queries due to inline existence checks and resource relationship counts.
- **Proposed change**:
  1. Add `withCount(['likes', 'comments'])` to the query in `ProjectController::index`.
  2. Eager load authenticated user's likes via:
     `with(['likes' => fn($q) => $q->where('user_id', $authUser->id)])`.
  3. Set `is_liked = $project->likes->isNotEmpty()` without querying DB in loop.
  4. In `ProjectResource`, check `isset($this->likes_count) ? $this->likes_count : $this->likes()->count()`.
- **Equivalence guarantee**: The integer count and boolean `is_liked` returned in JSON are 100% mathematically identical, but queries drop from $62$ to $4$.

---

## 9. Implementation checklist

- [ ] User approved module plan.
- [ ] Baseline tests written and passing.
- [ ] Eager loading and `withCount` optimizations implemented and verified.
- [ ] Dedicated `ProjectService` and `CommentService` implemented.
- [ ] Query count verified before and after.
- [ ] Completion report updated.

---

## 10. Completion report

Final status: NOT STARTED
