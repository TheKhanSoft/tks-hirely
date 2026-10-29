<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\BasicInfo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Traits\WithDynamicListing;

class CandidateProfileManagement extends Component
{
    use WithDynamicListing;

    // View state
    public $isFormOpen = false;
    public $isEditMode = false;
    public $isDetailsOpen = false;
    public $selectedRecordId;
    public $selectedRecordDetails = null;

    // Default per-page for card grid (16 items)
    public $perPage = 16;

    // Search & Filters
    public $search = '';
    public $trackFilter = '';
    public $lockFilter = '';

    // Collections
    public $availableUsers = [];

    // Form fields
    public $bi_id;
    public $bi_user_id;
    public $bi_track_type = 'ACADEMIC';
    public $bi_first_name;
    public $bi_last_name;
    public $bi_cnic_number;
    public $bi_dob;
    public $bi_is_locked = false;

    public function mount()
    {
        abort_unless(Auth::check() && Auth::user()->can('view-basic-info'), 403);
        $this->loadAuxiliaryData();
    }

    public function loadAuxiliaryData()
    {
        // 🔒 DYNAMIC CANDIDATE POOL: Only pure applicant accounts who do not hold any internal/institutional roles.
        // Works dynamically for ANY existing or newly added roles (dean, vice-chancellor, director, etc.)
        $this->availableUsers = User::doesntHave('basicInfo')
            ->whereHas('roles', fn($q) => $q->where('name', 'applicant'))
            ->whereDoesntHave('roles', fn($q) => $q->where('name', '!=', 'applicant'))
            ->get();
    }

    public function resetForm()
    {
        $this->reset([
            'bi_id',
            'bi_user_id',
            'bi_first_name',
            'bi_last_name',
            'bi_cnic_number',
            'bi_dob',
            'bi_is_locked',
        ]);
        $this->bi_track_type = 'ACADEMIC';
        $this->isFormOpen = false;
        $this->isEditMode = false;
    }

    public function openCreateBasicInfoForm()
    {
        abort_unless(Auth::user()->can('create-basic-info'), 403);
        $this->resetForm();
        $this->loadAuxiliaryData();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditBasicInfoForm($id)
    {
        abort_unless(Auth::user()->can('edit-basic-info'), 403);
        $this->resetForm();
        $this->bi_id = $id;
        $basicInfo = BasicInfo::findOrFail($id);
        $this->bi_user_id = $basicInfo->user_id;
        $this->bi_track_type = $basicInfo->track_type;
        $this->bi_first_name = $basicInfo->first_name;
        $this->bi_last_name = $basicInfo->last_name;
        $this->bi_cnic_number = $basicInfo->cnic_number;
        $this->bi_dob = $basicInfo->date_of_birth?->format('Y-m-d');
        $this->bi_is_locked = (bool)$basicInfo->is_locked;
        $this->isEditMode = true;
        $this->isFormOpen = true;
    }

    public function saveBasicInfo()
    {
        if ($this->isEditMode) {
            abort_unless(Auth::user()->can('edit-basic-info'), 403);
            $this->validate([
                'bi_first_name' => 'required|string|max:100',
                'bi_last_name' => 'required|string|max:100',
                'bi_cnic_number' => 'required|string|max:15|unique:basic_infos,cnic_number,' . $this->bi_id,
                'bi_dob' => 'required|date',
            ]);

            $basicInfo = BasicInfo::findOrFail($this->bi_id);
            $basicInfo->update([
                'first_name' => $this->bi_first_name,
                'last_name' => $this->bi_last_name,
                'cnic_number' => $this->bi_cnic_number,
                'date_of_birth' => $this->bi_dob,
                'is_locked' => $this->bi_is_locked,
            ]);
            session()->flash('message', 'Candidate profile updated successfully.');
        } else {
            abort_unless(Auth::user()->can('create-basic-info'), 403);
            $this->validate([
                'bi_user_id' => 'required|exists:users,id|unique:basic_infos,user_id',
                'bi_first_name' => 'required|string|max:100',
                'bi_last_name' => 'required|string|max:100',
                'bi_cnic_number' => 'required|string|max:15|unique:basic_infos,cnic_number',
                'bi_dob' => 'required|date',
            ]);

            BasicInfo::create([
                'user_id' => $this->bi_user_id,
                'track_type' => $this->bi_track_type,
                'first_name' => $this->bi_first_name,
                'last_name' => $this->bi_last_name,
                'cnic_number' => $this->bi_cnic_number,
                'date_of_birth' => $this->bi_dob,
                'is_locked' => $this->bi_is_locked,
            ]);
            session()->flash('message', 'Candidate profile created successfully.');
        }

        $this->resetForm();
        $this->loadAuxiliaryData();
    }

    public function toggleLock($id)
    {
        abort_unless(Auth::user()->can('lock-candidate-profile') || Auth::user()->can('edit-basic-info'), 403);
        $basicInfo = BasicInfo::findOrFail($id);
        $basicInfo->update(['is_locked' => !$basicInfo->is_locked]);
        session()->flash('message', 'Candidate lock status toggled.');
    }

    public function deleteBasicInfo($id)
    {
        abort_unless(Auth::user()->can('delete-basic-info'), 403);
        BasicInfo::destroy($id);
        session()->flash('message', 'Candidate profile deleted successfully.');
        $this->loadAuxiliaryData();
    }

    public function viewBasicInfoDetails($id)
    {
        abort_unless(Auth::user()->can('view-candidate-dossier') || Auth::user()->can('view-basic-info'), 403);
        $this->selectedRecordId = $id;
        $this->selectedRecordDetails = BasicInfo::with([
            'user',
            'degrees',
            'experiences',
            'medals',
            'publications',
            'events',
            'researchProjects'
        ])->findOrFail($id);
        $this->isDetailsOpen = true;
    }

    public function exportCsv()
    {
        abort_unless(Auth::user()->can('view-basic-info'), 403);
        $applicantScope = function ($uq) {
            $uq->where(function ($sub) {
                $sub->whereHas('roles', fn($rq) => $rq->where('name', 'applicant'))
                    ->orWhereHas('applications');
            })->whereDoesntHave('roles', fn($rq) => $rq->where('name', '!=', 'applicant'));
        };

        $profiles = BasicInfo::whereHas('user', $applicantScope)
            ->with(['user', 'user.applications', 'degrees'])
            ->orderBy('first_name')
            ->get();

        $headers = ['ID', 'Full Name', 'Email', 'CNIC', 'Track Type', 'Locked Status', 'Applications Count', 'Degrees Count'];
        $rows = [];
        foreach ($profiles as $p) {
            $rows[] = [
                $p->id,
                $p->first_name . ' ' . $p->last_name,
                $p->user?->email ?? 'N/A',
                $p->cnic_number,
                $p->track_type,
                $p->is_locked ? 'Locked' : 'Unlocked',
                $p->user?->applications?->count() ?? 0,
                $p->degrees?->count() ?? 0,
            ];
        }

        return $this->streamCsvDownload('candidate-profiles-' . date('Y-m-d') . '.csv', $headers, $rows);
    }

    public function render()
    {
        // 🔒 DYNAMIC CANDIDATE QUERY:
        // Only queries genuine job applicants/candidates.
        // Users holding ANY non-applicant role (dean, provost, registrar, committee, reviewer, or ANY custom role)
        // are automatically excluded without any hardcoded role lists!
        $applicantScope = function ($uq) {
            $uq->where(function ($sub) {
                $sub->whereHas('roles', fn($rq) => $rq->where('name', 'applicant'))
                    ->orWhereHas('applications');
            })->whereDoesntHave('roles', fn($rq) => $rq->where('name', '!=', 'applicant'));
        };

        $query = BasicInfo::whereHas('user', $applicantScope)
            ->with(['user.roles', 'user.applications.campaign', 'degrees']);

        if (!empty($this->trackFilter)) {
            $query->where('track_type', $this->trackFilter);
        }

        if ($this->lockFilter !== '') {
            $query->where('is_locked', (bool)$this->lockFilter);
        }

        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', $searchTerm)
                  ->orWhere('last_name', 'like', $searchTerm)
                  ->orWhere('cnic_number', 'like', $searchTerm)
                  ->orWhereHas('user', function ($uq) use ($searchTerm) {
                      $uq->where('email', 'like', $searchTerm);
                  });
            });
        }

        $profiles = $query->orderBy('first_name')->paginate($this->perPage);

        // Dynamic Candidate Metrics strictly scoped to genuine applicant pool
        $baseCandidateQuery = BasicInfo::whereHas('user', $applicantScope);

        $stats = [
            'total' => (clone $baseCandidateQuery)->count(),
            'with_applications' => (clone $baseCandidateQuery)->whereHas('user.applications')->count(),
            'academic' => (clone $baseCandidateQuery)->where('track_type', 'ACADEMIC')->count(),
            'locked' => (clone $baseCandidateQuery)->where('is_locked', true)->count(),
        ];

        return view('livewire.admin.candidate-profile-management', [
            'profiles' => $profiles,
            'stats' => $stats,
        ])->layout('layouts.app', ['title' => 'Candidate Profiles - Hirely']);
    }
}
