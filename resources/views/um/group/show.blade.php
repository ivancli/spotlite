<h3>Show Group</h3>
<div class="alert alert-info" role="alert">
    <strong>{{count($group->users)}}</strong> {{str_plural('user', count($group->users))}} in this group.
</div>
<table class="table table-bordered table-hover table-striped">
    <tbody>
    <tr>
        <th>ID</th>
        <td>{{$group->group_id}}</td>
    </tr>
    <tr>
        <th>Name</th>
        <td>{{$group->name}}</td>
    </tr>
    <tr>
        <th>Is active</th>
        <td>{{$group->active == 1 ? "Yes" : "No"}}</td>
    </tr>
    <tr>
        <th>Description</th>
        <td>{{$group->description}}</td>
    </tr>
    <tr>
        <th>Created At</th>
        <td>{{$group->created_at}}</td>
    </tr>
    <tr>
        <th>Updated At</th>
        <td>{{$group->updated_at}}</td>
    </tr>
    <tr>
        <th>Users</th>
        <td>
            @foreach($group->users as $index=>$user)
                <a href="{{$user->urls['show']}}">{{$user->name}}</a>
                @if($index != count($group->users)-1)
                    ,
                @endif
            @endforeach
        </td>
    </tr>
    </tbody>
</table>
