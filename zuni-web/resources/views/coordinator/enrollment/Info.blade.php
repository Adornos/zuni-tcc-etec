<div class="card bg-base-100 shadow-xl col-span-4 row-span-4 p-[2vmax]">

    <h2>Informações da Matricula</h2>

    <p>{{$enrollmentInfo->id}}</p>
    <p>{{$enrollmentInfo->student_id}}</p>
    <p>{{$enrollmentInfo->guardian->name}}</p>
    <p>{{$enrollmentInfo->status}}</p>
    <p>{{$enrollmentInfo->name}}</p>
    <p>{{$enrollmentInfo->birth_date}}</p>
    <p>{{$enrollmentInfo->gender}}</p>
    <p>{{$enrollmentInfo->class}}</p>
    <p>{{$enrollmentInfo->age}}</p>
    <p>{{$enrollmentInfo->street}}</p>
    <p>{{$enrollmentInfo->number}}</p>
    <p>{{$enrollmentInfo->district}}</p>
    <p>{{$enrollmentInfo->city}}</p>
    <p>{{$enrollmentInfo->state}}</p>
    <p>{{$enrollmentInfo->neurodivergent}}</p>
    <p>{{$enrollmentInfo->allergy}}</p>
    <p>{{$enrollmentInfo->food_restriction}}</p>
    <p>{{$enrollmentInfo->special_care}}</p>
    <p>{{$enrollmentInfo->notes}}</p>
    <p>{{$enrollmentInfo->created_at}}</p>
    <p>{{$enrollmentInfo->updated_at}}</p>

</div>