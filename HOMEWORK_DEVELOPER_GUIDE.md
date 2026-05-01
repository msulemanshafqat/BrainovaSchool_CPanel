# Homework Dashboard - Developer Quick Reference

## Quick Start

### Testing the Dashboard
1. Log in as Admin or Teacher
2. Navigate to `Homework & Tasks` (admin.php route: `homework.index`)
3. You should see:
   - 4 stat cards at top
   - Filter bar with dropdowns
   - Empty results area (will appear after "Proceed")

### Testing Filters
1. Select a Class from dropdown → Sections will load
2. Select a Section → Subjects will load
3. Optionally select Subject and Task Type
4. Click "Proceed" button
5. Charts and table should appear with filtered data

## Critical File Locations

| Component | File Path |
|-----------|-----------|
| Repository Methods | `app/Repositories/Homework/HomeworkRepository.php` |
| Controller Methods | `app/Http/Controllers/Admin/HomeworkController.php` |
| Routes | `routes/admin.php` |
| Main View | `resources/views/backend/homework/index.blade.php` |
| Table Partial | `resources/views/backend/homework/partials/filtered-table.blade.php` |
| Documentation | `HOMEWORK_REFACTOR_DOCUMENTATION.md` (this file parent) |

## Code Snippets

### Using the Repository in a New Controller
```php
use App\Repositories\Homework\HomeworkRepository;

class MyController {
    private $homeworkRepo;
    
    public function __construct(HomeworkRepository $repo) {
        $this->homeworkRepo = $repo;
    }
    
    public function getStats() {
        $stats = $this->homeworkRepo->getGlobalStats();
        return view('my-view', compact('stats'));
    }
}
```

### AJAX Call from Frontend
```javascript
$.ajax({
    url: '{{ route("homework.ajax.filtered-report") }}',
    method: 'POST',
    data: {
        class: classId,
        section: sectionId,
        subject: subjectId,
        task_type: taskType,
        _token: '{{ csrf_token() }}'
    },
    success: function(response) {
        // response.table_html
        // response.donut_data
        // response.trend_data
    }
});
```

### Adding a New Filter
In `index.blade.php`, add a new select in the filter bar:
```html
<div class="col-md-3">
  <label class="form-label small fw-700 mb-2">New Filter</label>
  <select id="filter-new" class="nice-select niceSelect bordered_style" style="width:100%">
    <option value="">All Options</option>
    {{-- options here --}}
  </select>
</div>
```

Then in the JavaScript, add to the filters object:
```javascript
const filters = {
    class: $('#filter-class').val(),
    section: $('#filter-section').val(),
    subject: $('#filter-subject').val(),
    task_type: $('#filter-task-type').val(),
    new_filter: $('#filter-new').val(),  // ADD THIS
};
```

And update the repository method signature:
```php
public function getFilteredHomeworkReport($filters): array {
    // ...
    if (!empty($filters['new_filter'])) {
        $query->where('new_filter_column', $filters['new_filter']);
    }
    // ...
}
```

## Common Tasks

### Modify the Donut Chart
File: `index.blade.php` (around line 450)
```javascript
donutChartInstance = new Chart(donutCtx, {
    type: 'doughnut',
    data: {
        labels: response.donut_data.labels,
        datasets: [{
            data: response.donut_data.data,
            backgroundColor: response.donut_data.colors,
            // Modify options here
            cutout: '62%',  // Donut hole size
            hoverOffset: 6  // Hover effect
        }]
    },
    // ... rest of config
});
```

### Change the Line Chart Colors
File: `HomeworkRepository.php` (around line 245)
```php
$colors = ['#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#06b6d4', '#ec4899', '#f97316'];
// Add or modify colors here
```

### Add Permission Checks
File: `HomeworkRepository.php` (around line 150)
```php
// Apply teacher permission scope if not admin
if (!auth()->user() || auth()->user()->role_id != 1) {
    $query->whereIn('subject_id', teacherSubjects());
    // Add additional restrictions here if needed
}
```

### Customize Stat Cards
File: `index.blade.php` (around line 60)
```html
<div class="col-md-6 col-lg-3">
    <div class="sc">
      <div class="si" style="background:#dbeafe">
        <i class="fa-solid fa-book" style="color:#1d4ed8"></i>
      </div>
      <div style="flex:1">
        <div class="sv" id="stat-total-tasks">—</div>
        <div class="sl">Total Tasks Assigned</div>
      </div>
    </div>
</div>
```

## Debugging

### Enable Query Logging
```php
// In routes or controller
use Illuminate\Support\Facades\DB;

DB::enableQueryLog();
// ... your code here
dd(DB::getQueryLog());
```

### Check CSRF Token
```javascript
// In browser console
$('meta[name="csrf-token"]').attr('content')
```

### Check AJAX Response
```javascript
$.ajax({
    // ...
    error: function(xhr) {
        console.log('Status:', xhr.status);
        console.log('Response:', xhr.responseJSON);
        console.log('Error:', xhr.responseText);
    }
});
```

### Inspect Chart Data
```javascript
// After chart creation
console.log('Donut Chart:', donutChartInstance.data);
console.log('Line Chart:', lineChartInstance.data);
```

## Database Tables Reference

### homework
- id
- classes_id (FK)
- section_id (FK)
- subject_id (FK)
- title
- task_type (quiz|homework|project|activity|game|assignment)
- marks
- submission_date
- date
- status
- session_id

### homework_students
- id
- homework_id (FK)
- student_id (FK)
- marks (nullable)
- date (submission date)
- feedback

### homework_quiz_questions
- id
- homework_id (FK)
- question
- option_a, option_b, option_c, option_d
- correct_answer
- hint (nullable)
- explanation (nullable)

### homework_quiz_answers
- id
- homework_id (FK)
- student_id (FK)
- question_id (FK)
- selected_answer
- is_correct

## Key Helpers Used

### teacherSubjects()
Returns array of subject IDs for current teacher
Location: `app/Traits/CommonHelperTrait.php` or similar

### setting('session')
Returns current session ID from settings
Location: `app/Helpers/SettingHelper.php` or similar

### hasPermission($permission)
Checks if current user has permission
Location: `app/Helpers/PermissionHelper.php` or similar

### auth()->user()
Gets current authenticated user

## Performance Tips

1. **Indexing**: Ensure database indexes exist on:
   - homework.classes_id
   - homework.section_id
   - homework.subject_id
   - homework.task_type
   - homework.session_id
   - homework_students.homework_id
   - homework_students.marks

2. **Eager Loading**: The repository uses `with()` to eager load relations:
   ```php
   $query = $this->model::with(['class', 'section', 'subject', 'upload'])
   ```

3. **Pagination**: Consider adding pagination for large datasets:
   ```php
   return $query->paginate(50);
   ```

4. **Caching**: Cache global stats for 5 minutes:
   ```php
   return Cache::remember('homework_stats', 300, function() {
       // stats calculation
   });
   ```

## Useful Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan view:clear
php artisan route:cache

# Fresh database migration
php artisan migrate:fresh --seed

# Create a new controller
php artisan make:controller Admin/NewController

# Create a new repository
php artisan make:class Repositories/NewRepo

# Test route
php artisan route:list --path=homework
```

## Dependency Versions (Required)

- Laravel 8.0+
- Chart.js 4.4.0+
- jQuery 3.0+
- Bootstrap 5.0+
- Nice Select (jQuery plugin)

## Security Considerations

1. **CSRF Protection**: All POST requests include `_token`
2. **Permission Checks**: All methods check user role and permissions
3. **Query Sanitization**: Uses query builder (parameterized queries)
4. **Role-Based Access**: Teachers scoped to their subjects
5. **Input Validation**: Filter values are validated before use

## Common Errors & Solutions

| Error | Cause | Solution |
|-------|-------|----------|
| "CSRF token mismatch" | Token not sent in AJAX | Include `_token` in form data |
| "Class not found" | Wrong relationship name | Check model `$with` property |
| "Chart not displaying" | Canvas element missing | Check element IDs match |
| "Sections not loading" | Route not registered | Verify routes/admin.php |
| "Permission denied" | User role check failed | Check user role_id in DB |
| "Undefined offset in array" | Empty filter results | Add empty check in view |

## Where to Add New Features

- **New Stat Card**: Add HTML in Section 1 + update loadGlobalStats()
- **New Filter**: Add select element + JavaScript handler
- **New Chart**: Add canvas element + Chart.js code
- **New Table Column**: Update partial + repository query
- **New AJAX Endpoint**: Add controller method + route

---

**Quick Links**:
- Main Documentation: `HOMEWORK_REFACTOR_DOCUMENTATION.md`
- Live Demo: `http://localhost/admin/homework`
- API Docs: See controller method docblocks

**Contact**: Development team
**Last Updated**: 2026-05-01
