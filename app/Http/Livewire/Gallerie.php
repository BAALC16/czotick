<?php

namespace App\Http\Livewire;

use Livewire\WithFileUploads;
use App\Models\gallerie as BaseGallerie;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use App\Models\Trace;

class Gallerie extends Component
{
    use WithFileUploads;
    public $newImage = [];
    public $addPhoto = null;
    public function render()
    {
        $albums=BaseGallerie::all();
        // $albums=DB::table('galleries')->paginate(3);
        return view('livewire.gallerie', ['photos'=>$albums]);    }

    protected $rules = [
        'newImage.idService' => 'required',
        'newImage.titre' => 'required',
        'newImage.addPhoto' => 'string',
        'newImage.image' => 'string',
    ];
    public function store()
    {
        $validationAttributes = $this->validate();
        $newImage = $validationAttributes['newImage'];

        if ($this->addPhoto != null) {
            $this->validate([
                'addPhoto' => 'image|max:10240', // 1MB Max
            ]);
            $image_path = $this->addPhoto->store('gallerie', 'public');
            $newImage['image'] = '/storage/' . $image_path;
        }

        $image = BaseGallerie::create($newImage);

        $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Uploader une image", 'description' => "Upload image ".$image->titre, 'resource' => $image->id]);
        $trace->save();

        $image = BaseGallerie::where("id", $image)->first();
        $this->reset();
        // return response()->json([
        //     "success" => true,
        //     "message" => 'Image   a bien été créé.',
        //     "redirect" => route('services.index'),
        // ]);


        return  redirect()->route('services.gallerie');

    }
    public function deleteImage($id)
    {
        $img_deleted = BaseGallerie::find($id);
        if ($img_deleted->image) {
            $impage_path = substr($img_deleted->image, 1, strlen($img_deleted->image)); //je supprime le premier caractère
            File::delete($impage_path);
        }
        $img_deleted = BaseGallerie::find($id);

        $trace = new Trace(['user_id' => auth()->id(), 'trace' => "Supprimer une image", 'description' => "Suppression image ".$img_deleted->titre, 'resource' => $img_deleted->id]);
        $trace->save();

        $img_deleted->delete();

        return  redirect()->route('services.gallerie');
    }
}
