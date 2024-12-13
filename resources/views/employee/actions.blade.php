<div class="d-flex">
    <a href="{{ route('employees.show', ['employee' =>$employee->id]) }}" class="btn btn-outline-dark btn-sm me-2">
        <i class="bi-person-lines-fill"></i>
    </a>
    <a href="{{ route('employees.edit', ['employee' => $employee->id]) }}" class="btn btn-outline-dark btn-sm me-2">
        <i class="bi-pencil-square"></i>
    </a>
    <a href="{{ route('employees.view', ['employee' => $employee->id]) }}" class="btn btn-outline-dark btn-sm me-2">
        <i class="bi-eye"></i>
    </a>
    <form action="{{ route('employees.destroy', ['employee' => $employee->id]) }}" method="POST" style="display:inline;">
        @csrf
        @method('delete')
        <button type="submit" class="btn btn-outline-dark btn-sm">
            <i class="bi-trash"></i>
        </button>
    </form>
</div>

