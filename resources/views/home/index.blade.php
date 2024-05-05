<!DOCTYPE html>
<html lang="en">

<head>
    @include('includes.header')
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <title>Taskboard</title>
</head>

<body class="body">
    @include('includes.navigation')
    <div class="container" style="margin-top: 100px">
        <h3 class="title">Task Log</h3>
        <div class="col-sm-12 col-md-12 form-row" style="width: 100%; justify-content: center">
            @if (!empty($success))
                <span style="margin-top: 10px" class="info_box text-success">{{ $success }}</span>
            @endif
        </div>
        <div class="col-sm-12 col-md-12 form-row" style="width: 100%; justify-content: center">
            @if (!empty($error))
                <span style="margin-top: 10px" class="info_box text-danger">{{ $error }}</span>
            @endif
        </div>
        <div class="row d-flex justify-content-center">
            <div class="col-md-6 ">
                <form action="{{ route('add_task') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" required class="form-control task_name" placeholder="Enter Your Task Name"
                            name="task_name" aria-label="Text input with segmented dropdown button">
                    </div>
                    <div class="input-group mb-3">
                        <select class="custom-select project" id="inputGroupSelect02" name="project" type="select"
                            required>
                            <option value="">Choose Project...</option>
                            <option value="1">School Management</option>
                            <option value="2">Hospital Management</option>
                            <option value="3">Hotel Management</option>
                        </select>

                        <select class="custom-select priority_level" id="inputGroupSelect02" type="text"
                            name="priority_level" placeholder="Select Priority Level">
                            <option value="0">Normal</option>
                            <option value="1">Urgent</option>
                            <option value="2">Hyper-critical</option>
                        </select>

                        <div class="input-group-append">
                            <button class="input-group-text add" type="submit" for="inputGroupSelect02">Add
                                Task</button>
                        </div>
                    </div>
                </form>

                <table class="table" id="taskList">

                    <thead>
                        <tr>
                            <th scope="col">S/N</th>
                            <th scope="col">Task Name</th>
                            <th scope="col">Tasks Priority</th>
                            <th scope="col">Edit</th>
                            <th scope="col">Delete</th>
                        </tr>
                    </thead>

                    <form action="{{ route('filter_tasks') }}" method="post">
                        @csrf
                        <div class="input-group mb-3" style="margin-bottom: 10px">
                            <select class="custom-select project" id="inputGroupSelect02" name="project" type="select">
                                <option value="">Filter Tasks By Projects...</option>
                                <option value="1">School Management</option>
                                <option value="2">Hospital Management</option>
                                <option value="3">Hotel Management</option>
                            </select>
                            <input type="hidden" id="projectId" name="project_id">
                            <div class="input-group-append">
                                <button class="input-group-text add" type="submit"
                                    for="inputGroupSelect02">Filter</button>
                            </div>
                        </div>
                    </form>


                    <tbody id="sortable">
                        @if (!$all_tasks->isEmpty())


                            @foreach ($all_tasks as $key => $tasks)
                                <tr class="table-row" data-task-id="{{ $tasks->id }}">
                                    <td scope="row">{{ $loop->index + 1 }}</td>
                                    <td class="truncate">{{ $tasks->task_name }}</td>
                                    <td>{{ $tasks->priority_level == 0 ? 'Normal' : ($tasks->priority_level == 1 ? 'Urgent' : 'Hyper-critical') }}
                                    </td>
                                    <td style="text-align: center" class="task" data-toggle="modal"
                                        data-target="#exampleModalCenter">
                                        <a type="button" class="edit" style="text-decoration: none"><i
                                                class="fa fa-pencil" aria-hidden="true"></i></a>
                                    </td>
                                    <td style="text-align: center">
                                        <a type="button" class="delete" data-task-id="{{ $tasks->id }}"
                                            style="text-decoration: none"><i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" style="text-align: center">No Task Available, Please Add One</td>
                            </tr>
                        @endif

                    </tbody>
                </table>

                <button type="button" class="btn btn-primary btn-lg btn-block" id="deleteAllTasksButton">Delete All
                    Tasks</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('edit_task') }}" method="post">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" required class="form-control task_name" placeholder="Task Name"
                                name="task_name" aria-label="Text input with segmented dropdown button">
                            <select class="custom-select priority_level" id="inputGroupSelect02" type="text"
                                name="priority_level" placeholder="Select Priority Level">
                                <option value="0">Normal</option>
                                <option value="1">Urgent</option>
                                <option value="2">Hyper-critical</option>
                            </select>
                            <input type="hidden" class="task_id" name="task_id">
                            <div class="input-group-append">
                                <button class="input-group-text add" type="submit" for="inputGroupSelect02">Edit
                                    Task</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var deleteIcons = document.querySelectorAll(".delete");

            deleteIcons.forEach(function(icon) {
                icon.addEventListener("click", function() {
                    var taskId = icon.getAttribute("data-task-id");
                    deleteTask(taskId);
                });
            });

            function deleteTask(taskId) {
                if (confirm("Are you sure you want to delete this task?")) {
                    fetch("/delete_task/" + taskId, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            },
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.success) {
                                Swal.fire({
                                    text: "Successfully Deleted",
                                    icon: "success",
                                    buttons: true,
                                });
                                window.location.reload();
                            } else {
                                alert(data.error);
                            }
                        })
                        .catch((error) => {
                            console.error("Error:", error);
                        });
                }
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            var deleteAllTasksButton = document.getElementById("deleteAllTasksButton");

            deleteAllTasksButton.addEventListener("click", function() {
                if (confirm("Are you sure you want to delete all tasks?")) {
                    deleteAllTasks();
                }
            });

            function deleteAllTasks() {
                fetch("{{ route('delete_all_tasks') }}", {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            console.log(data.message);
                        } else {
                            console.error(data.error);
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                    });
            }
        });
    </script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="{{ asset('assets/js/index.js') }}"></script>
</body>

</html>
