<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\Advertisement;
use App\Models\BasicInfo;
use App\Models\ReviewerProfile;
use App\Models\BillingLedger;
use App\Models\User;
use App\Models\CommitteeConstitution;
use App\Models\RecruitmentProcessFlow;

class UniversalSearchPalette extends Component
{
    public bool $isOpen = false;
    public string $query = '';
    public string $activeCategory = 'all';
    public int $selectedIndex = 0;

    #[On('open-search-palette')]
    public function openPalette(): void
    {
        $this->isOpen = true;
        $this->selectedIndex = 0;
    }

    #[On('close-search-palette')]
    public function closePalette(): void
    {
        $this->isOpen = false;
        $this->query = '';
        $this->selectedIndex = 0;
    }

    public function togglePalette(): void
    {
        $this->isOpen = !$this->isOpen;
        if (!$this->isOpen) {
            $this->query = '';
        }
        $this->selectedIndex = 0;
    }

    public function selectCategory(string $category): void
    {
        $this->activeCategory = $category;
        $this->selectedIndex = 0;
    }

    public function updatedQuery(): void
    {
        $this->selectedIndex = 0;
    }

    /**
     * Get categories permitted for current user.
     */
    public function getAvailableCategories(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $isSuperAdmin = $user->hasRole('super-admin');
        $isRegistrar = $user->hasRole('registrar');

        $categories = [
            'all' => ['label' => 'All Results', 'icon' => 'sparkles'],
            'navigation' => ['label' => 'Pages & Modules', 'icon' => 'layout'],
        ];

        if ($isSuperAdmin || $isRegistrar || $user->can('view-campaign') || $user->hasRole('applicant')) {
            $categories['campaigns'] = ['label' => 'Job Campaigns', 'icon' => 'briefcase'];
        }

        if ($isSuperAdmin || $isRegistrar || $user->can('view-campaign')) {
            $categories['advertisements'] = ['label' => 'Advertisements', 'icon' => 'calendar'];
        }

        if ($isSuperAdmin || $isRegistrar || $user->can('view-application') || $user->hasRole('applicant') || $user->can('scrutinize-application')) {
            $categories['applications'] = ['label' => 'Applications', 'icon' => 'file-text'];
        }

        if ($isSuperAdmin || $isRegistrar || $user->can('view-basic-info')) {
            $categories['candidates'] = ['label' => 'Candidate Profiles', 'icon' => 'users'];
        }

        if ($isSuperAdmin || $isRegistrar || $user->can('manage-reviewers')) {
            $categories['reviewers'] = ['label' => 'Reviewers & Experts', 'icon' => 'award'];
        }

        if ($isSuperAdmin || $isRegistrar || $user->can('view-payment')) {
            $categories['payments'] = ['label' => 'Payments & Vouchers', 'icon' => 'credit-card'];
        }

        if ($isSuperAdmin || $isRegistrar || $user->can('tune-constitution')) {
            $categories['constitutions'] = ['label' => 'Boards & Pipelines', 'icon' => 'sliders'];
        }

        if ($isSuperAdmin || $isRegistrar || $user->can('view-user')) {
            $categories['users'] = ['label' => 'User Accounts', 'icon' => 'shield'];
        }

        return $categories;
    }

    /**
     * Build unified search results based strictly on user permissions.
     */
    public function getSearchResults(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $isSuperAdmin = $user->hasRole('super-admin');
        $isRegistrar = $user->hasRole('registrar');
        $isStaffAdmin = $isSuperAdmin || $isRegistrar || $user->hasRole('admin');
        $isScrutiny = $user->hasAnyRole(['scrutiny-committee', 'scrutiny']) || $user->can('scrutinize-application');
        $isBoard = $user->hasAnyRole(['selection-board-member', 'board-member', 'board']) || $user->can('access-board-grading') || $user->can('evaluate-application');
        $isAppellate = $user->hasAnyRole(['appellate-committee', 'appellate']) || $user->can('view-appeal') || $user->can('process-appeal');
        $isReviewer = $user->hasAnyRole(['academic-reviewer', 'reviewer']) || $user->can('evaluate-academic-dossier');
        $isApplicant = $user->hasRole('applicant');
        $isApplicantOnly = $isApplicant && !$isStaffAdmin;

        $baseRoute = ($isRegistrar && !$isSuperAdmin) ? '/registrar' : '/admin';
        $searchTerm = trim($this->query);
        $hasSearch = strlen($searchTerm) > 0;
        $likeTerm = '%' . $searchTerm . '%';

        $results = [];

        // 1. Navigation / Pages & Workspaces
        if ($this->activeCategory === 'all' || $this->activeCategory === 'navigation') {
            $navItems = [];

            if ($isSuperAdmin || $isRegistrar) {
                $navItems[] = [
                    'id' => 'nav_desk',
                    'title' => 'Operations Desk',
                    'subtitle' => 'Unified executive command dashboard & recruitment metrics',
                    'url' => "$baseRoute/dashboard",
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Desk',
                    'badge_style' => 'badge-indigo',
                    'icon' => 'layout',
                    'keywords' => 'dashboard overview home admin registrar stats',
                ];
            }

            if ($isSuperAdmin || $isRegistrar || $user->can('view-campaign')) {
                $navItems[] = [
                    'id' => 'nav_adv',
                    'title' => 'Advertisements Management',
                    'subtitle' => 'Publish cycles, validity dates, newspaper references & corrigenda',
                    'url' => "$baseRoute/advertisements",
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Cycles',
                    'badge_style' => 'badge-indigo',
                    'icon' => 'calendar',
                    'keywords' => 'advertisement newspaper cycle date extension',
                ];
                $navItems[] = [
                    'id' => 'nav_camp',
                    'title' => 'Job Campaigns & Posts',
                    'subtitle' => 'Recruitment posts, eligibility criteria, department quotas & status',
                    'url' => "$baseRoute/campaigns",
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Jobs',
                    'badge_style' => 'badge-indigo',
                    'icon' => 'briefcase',
                    'keywords' => 'campaign jobs positions vacancies tenure track faculty',
                ];
            }

            if ($isSuperAdmin || $isRegistrar || $user->can('tune-constitution')) {
                $navItems[] = [
                    'id' => 'nav_const',
                    'title' => 'Boards & Constitutions',
                    'subtitle' => 'Statutory committee composition, member quotas & criteria',
                    'url' => "$baseRoute/criteria",
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Statutory',
                    'badge_style' => 'badge-cyan',
                    'icon' => 'sliders',
                    'keywords' => 'constitution criteria committee board quota',
                ];
                $navItems[] = [
                    'id' => 'nav_pipe',
                    'title' => 'Recruitment Pipelines & Stages',
                    'subtitle' => 'Dynamic recruitment stages, flow routing & milestone transitions',
                    'url' => "$baseRoute/pipelines",
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Pipeline',
                    'badge_style' => 'badge-cyan',
                    'icon' => 'sliders',
                    'keywords' => 'pipeline stages process flow routing transitions',
                ];
            }

            if ($isSuperAdmin || $isRegistrar || $user->can('manage-statuses') || $user->can('manage-settings')) {
                $navItems[] = [
                    'id' => 'nav_status',
                    'title' => 'Status Lifecycles & Workflow',
                    'subtitle' => 'Configure system-wide dynamic status codes, colors & rules',
                    'url' => "$baseRoute/statuses",
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Workflow',
                    'badge_style' => 'badge-amber',
                    'icon' => 'activity',
                    'keywords' => 'status lifecycle transitions workflow rules',
                ];
            }

            if ($isStaffAdmin || ($user->can('view-application') && !$isApplicantOnly)) {
                $navItems[] = [
                    'id' => 'nav_app',
                    'title' => 'Applications Dossiers Desk',
                    'subtitle' => 'Candidate submissions, dossier review, referee & payment status',
                    'url' => "$baseRoute/applications",
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Records',
                    'badge_style' => 'badge-emerald',
                    'icon' => 'file-text',
                    'keywords' => 'application candidate submissions dossier tracking',
                ];
            }

            if ($isSuperAdmin || $isRegistrar || $user->can('view-basic-info')) {
                $navItems[] = [
                    'id' => 'nav_cand',
                    'title' => 'Candidate Profiles Directory',
                    'subtitle' => 'Bio-data, degrees, publications, experience & lock state',
                    'url' => "$baseRoute/candidates",
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Candidates',
                    'badge_style' => 'badge-indigo',
                    'icon' => 'users',
                    'keywords' => 'candidates profiles cnic bio biodata research medals',
                ];
            }

            if ($isSuperAdmin || $isRegistrar || $user->can('view-payment')) {
                $navItems[] = [
                    'id' => 'nav_pay',
                    'title' => 'Payments & Billing Ledgers',
                    'subtitle' => 'Kuickpay 1Link transaction logs, fee challans & reconciliations',
                    'url' => "$baseRoute/payments",
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Fintech',
                    'badge_style' => 'badge-emerald',
                    'icon' => 'credit-card',
                    'keywords' => 'payments fee challan kuickpay 1link voucher consumer ledger',
                ];
            }

            if ($isSuperAdmin || $isRegistrar || $user->can('manage-reviewers')) {
                $navItems[] = [
                    'id' => 'nav_rev',
                    'title' => 'Academic Reviewers & Experts',
                    'subtitle' => 'Empanelled international/national referees, expertise & reports',
                    'url' => "$baseRoute/reviewers",
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Experts',
                    'badge_style' => 'badge-purple',
                    'icon' => 'award',
                    'keywords' => 'reviewers referees experts academics peer review',
                ];
            }

            if ($isSuperAdmin || $isRegistrar || $user->can('view-user')) {
                $navItems[] = [
                    'id' => 'nav_users',
                    'title' => 'User Accounts Management',
                    'subtitle' => 'Manage operator profiles, email credentials & direct permissions',
                    'url' => "$baseRoute/users",
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Accounts',
                    'badge_style' => 'badge-blue',
                    'icon' => 'shield',
                    'keywords' => 'users accounts personnel passwords operators staff',
                ];
            }

            if ($isSuperAdmin || $user->can('view-role') || $user->can('view-permission')) {
                $navItems[] = [
                    'id' => 'nav_roles',
                    'title' => 'Roles & Access Governance',
                    'subtitle' => 'Spatie RBAC matrix, permission assignments & security policies',
                    'url' => "$baseRoute/roles",
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Security',
                    'badge_style' => 'badge-rose',
                    'icon' => 'shield',
                    'keywords' => 'roles permissions spatie rbac access security privileges',
                ];
            }

            if ($isSuperAdmin || $isRegistrar || $user->can('view-audit-log')) {
                $navItems[] = [
                    'id' => 'nav_audit',
                    'title' => 'Security Audit Logs',
                    'subtitle' => 'Tamper-evident system activity, model mutations & user actions',
                    'url' => "$baseRoute/audit-logs",
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Audit',
                    'badge_style' => 'badge-amber',
                    'icon' => 'activity',
                    'keywords' => 'audit logs events mutations security history',
                ];
            }

            if ($isSuperAdmin || $isRegistrar || $user->can('manage-settings')) {
                $navItems[] = [
                    'id' => 'nav_settings',
                    'title' => 'System Settings & Workflows',
                    'subtitle' => 'Institution branding, mail server, fee structures & parameters',
                    'url' => "$baseRoute/settings",
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Config',
                    'badge_style' => 'badge-blue',
                    'icon' => 'settings',
                    'keywords' => 'settings branding smtp fees preferences configuration',
                ];
            }

            if ($isSuperAdmin || $isRegistrar) {
                $navItems[] = [
                    'id' => 'nav_bank',
                    'title' => '1Link Fintech Simulator Sandbox',
                    'subtitle' => 'Test real-time Kuickpay webhook payments & voucher inquiry',
                    'url' => '/bank/simulator',
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Sandbox',
                    'badge_style' => 'badge-cyan',
                    'icon' => 'credit-card',
                    'keywords' => 'simulator bank 1link kuickpay sandbox test voucher',
                ];
            }

            if ($isSuperAdmin || $isScrutiny) {
                $navItems[] = [
                    'id' => 'nav_scrutiny',
                    'title' => 'Scrutiny Committee Desk',
                    'subtitle' => 'Pre-qualification verification, document scrutiny & deficiency marks',
                    'url' => '/scrutiny/dashboard',
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Scrutiny',
                    'badge_style' => 'badge-purple',
                    'icon' => 'file-text',
                    'keywords' => 'scrutiny verification dossier evaluation eligibility',
                ];
            }

            if ($isSuperAdmin || $isBoard) {
                $navItems[] = [
                    'id' => 'nav_board',
                    'title' => 'Selection Board Desk',
                    'subtitle' => 'Live candidate grading, interview score sheets & statutory rankings',
                    'url' => '/board/dashboard',
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Board',
                    'badge_style' => 'badge-amber',
                    'icon' => 'award',
                    'keywords' => 'board interview grading marks evaluation syndicate',
                ];
            }

            if ($isSuperAdmin || $isAppellate) {
                $navItems[] = [
                    'id' => 'nav_appeal',
                    'title' => 'Appellate Committee Appeals',
                    'subtitle' => 'Review applicant disqualification appeals, grievance hearings & redressal',
                    'url' => '/appellate/dashboard',
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Appeals',
                    'badge_style' => 'badge-rose',
                    'icon' => 'file-text',
                    'keywords' => 'appeals grievances dispute appellate hearing',
                ];
            }

            if ($isSuperAdmin || $isReviewer) {
                $navItems[] = [
                    'id' => 'nav_reviewer',
                    'title' => 'Academic Peer Review Desk',
                    'subtitle' => 'Independent academic evaluation of candidate dossiers & publications',
                    'url' => '/reviewer/dashboard',
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Review',
                    'badge_style' => 'badge-cyan',
                    'icon' => 'award',
                    'keywords' => 'peer review academic evaluation referee scores',
                ];
            }

            if ($isApplicant) {
                $navItems[] = [
                    'id' => 'nav_app_profile',
                    'title' => 'Candidate Profile Workspace',
                    'subtitle' => 'Manage personal bio, academic degrees, research papers & documents',
                    'url' => '/applicant/dashboard',
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Profile',
                    'badge_style' => 'badge-indigo',
                    'icon' => 'users',
                    'keywords' => 'applicant candidate profile degrees cv bio qualifications',
                ];
                $navItems[] = [
                    'id' => 'nav_app_wizard',
                    'title' => 'Application Wizard / Apply',
                    'subtitle' => 'Multi-step application submission, track selection & voucher generation',
                    'url' => '/applicant/wizard',
                    'category' => 'Navigation',
                    'category_key' => 'navigation',
                    'badge' => 'Apply',
                    'badge_style' => 'badge-emerald',
                    'icon' => 'briefcase',
                    'keywords' => 'apply wizard form submit candidature job',
                ];
            }

            // Filter nav items by search term if provided
            foreach ($navItems as $item) {
                if ($hasSearch) {
                    $searchPool = strtolower($item['title'] . ' ' . $item['subtitle'] . ' ' . $item['keywords'] . ' ' . $item['badge']);
                    if (str_contains($searchPool, strtolower($searchTerm))) {
                        $results[] = $item;
                    }
                } else {
                    $results[] = $item;
                }
            }
        }

        // 2. Job Campaigns
        if ($this->activeCategory === 'all' || $this->activeCategory === 'campaigns') {
            if ($isSuperAdmin || $isRegistrar || $user->can('view-campaign')) {
                $cQuery = Campaign::with('advertisement')->orderBy('created_at', 'desc');
                if ($hasSearch) {
                    $cQuery->where(function ($q) use ($likeTerm) {
                        $q->where('title', 'like', $likeTerm)
                          ->orWhere('job_code', 'like', $likeTerm)
                          ->orWhere('department', 'like', $likeTerm)
                          ->orWhereHas('advertisement', fn($aq) => $aq->where('advertisement_no', 'like', $likeTerm));
                    });
                }
                $campaigns = $cQuery->limit(6)->get();

                foreach ($campaigns as $camp) {
                    $advNo = $camp->advertisement?->advertisement_no ? "Adv: {$camp->advertisement->advertisement_no} • " : '';
                    $results[] = [
                        'id' => 'camp_' . $camp->id,
                        'title' => $camp->title,
                        'subtitle' => "{$advNo}Dept: {$camp->department} • Code: {$camp->job_code}",
                        'url' => "$baseRoute/campaigns?search=" . urlencode($camp->job_code ?? $camp->title),
                        'category' => 'Job Campaigns',
                        'category_key' => 'campaigns',
                        'badge' => $camp->is_active ? 'ACTIVE' : 'INACTIVE',
                        'badge_style' => $camp->is_active ? 'badge-emerald' : 'badge-slate',
                        'icon' => 'briefcase',
                        'meta' => $camp->closing_date ? 'Closes ' . $camp->closing_date->format('d M Y') : 'Open',
                    ];
                }
            } elseif ($isApplicant) {
                // Applicants only see active and open campaigns
                $cQuery = Campaign::where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('closing_date')->orWhere('closing_date', '>=', now());
                    })
                    ->with('advertisement')
                    ->orderBy('created_at', 'desc');

                if ($hasSearch) {
                    $cQuery->where(function ($q) use ($likeTerm) {
                        $q->where('title', 'like', $likeTerm)
                          ->orWhere('job_code', 'like', $likeTerm)
                          ->orWhere('department', 'like', $likeTerm);
                    });
                }
                $campaigns = $cQuery->limit(6)->get();

                foreach ($campaigns as $camp) {
                    $results[] = [
                        'id' => 'camp_' . $camp->id,
                        'title' => $camp->title,
                        'subtitle' => "Department: {$camp->department} • Code: {$camp->job_code}",
                        'url' => "/applicant/wizard?job=" . $camp->id,
                        'category' => 'Job Campaigns',
                        'category_key' => 'campaigns',
                        'badge' => 'OPEN',
                        'badge_style' => 'badge-emerald',
                        'icon' => 'briefcase',
                        'meta' => $camp->closing_date ? 'Deadline ' . $camp->closing_date->format('d M Y') : 'Apply Now',
                    ];
                }
            }
        }

        // 3. Official Advertisements
        if ($this->activeCategory === 'all' || $this->activeCategory === 'advertisements') {
            if ($isSuperAdmin || $isRegistrar || $user->can('view-campaign')) {
                $aQuery = Advertisement::orderBy('year', 'desc')->orderBy('created_at', 'desc');
                if ($hasSearch) {
                    $aQuery->where(function ($q) use ($likeTerm) {
                        $q->where('advertisement_no', 'like', $likeTerm)
                          ->orWhere('title', 'like', $likeTerm)
                          ->orWhere('year', 'like', $likeTerm)
                          ->orWhere('newspaper_reference', 'like', $likeTerm);
                    });
                }
                $advertisements = $aQuery->limit(5)->get();

                foreach ($advertisements as $adv) {
                    $results[] = [
                        'id' => 'adv_' . $adv->id,
                        'title' => "Advertisement {$adv->advertisement_no} ({$adv->year})",
                        'subtitle' => $adv->title ?: 'Official University Employment Advertisement',
                        'url' => "$baseRoute/advertisements?search=" . urlencode($adv->advertisement_no),
                        'category' => 'Advertisements',
                        'category_key' => 'advertisements',
                        'badge' => $adv->is_active ? 'ACTIVE' : 'EXPIRED',
                        'badge_style' => $adv->is_active ? 'badge-indigo' : 'badge-slate',
                        'icon' => 'calendar',
                        'meta' => $adv->valid_to ? 'Valid till ' . $adv->valid_to->format('d M Y') : 'Year ' . $adv->year,
                    ];
                }
            }
        }

        // 4. Applications Dossiers
        if ($this->activeCategory === 'all' || $this->activeCategory === 'applications') {
            if ($isStaffAdmin || ($user->can('view-application') && !$isApplicantOnly)) {
                $appQuery = Application::with(['campaign.advertisement', 'user.basicInfo', 'billingLedger'])
                    ->orderBy('created_at', 'desc');

                if ($hasSearch) {
                    $appQuery->where(function ($q) use ($likeTerm) {
                        $q->where('id', 'like', $likeTerm)
                          ->orWhere('status', 'like', $likeTerm)
                          ->orWhereHas('user', function ($uq) use ($likeTerm) {
                              $uq->where('email', 'like', $likeTerm)
                                 ->orWhereHas('basicInfo', function ($bq) use ($likeTerm) {
                                     $bq->where('first_name', 'like', $likeTerm)
                                        ->orWhere('last_name', 'like', $likeTerm)
                                        ->orWhere('cnic_number', 'like', $likeTerm);
                                 });
                          })
                          ->orWhereHas('campaign', function ($cq) use ($likeTerm) {
                              $cq->where('title', 'like', $likeTerm)
                                 ->orWhere('job_code', 'like', $likeTerm);
                          })
                          ->orWhereHas('billingLedger', function ($lq) use ($likeTerm) {
                              $lq->where('consumer_id', 'like', $likeTerm)
                                 ->orWhere('transaction_id', 'like', $likeTerm);
                          });
                    });
                }

                $applications = $appQuery->limit(6)->get();

                foreach ($applications as $app) {
                    $candName = $app->user?->basicInfo ? trim(($app->user->basicInfo->first_name ?? '') . ' ' . ($app->user->basicInfo->last_name ?? '')) : ($app->user?->email ?? 'Applicant');
                    $cnic = $app->user?->basicInfo?->cnic_number ? "CNIC: {$app->user->basicInfo->cnic_number} • " : '';
                    $results[] = [
                        'id' => 'app_' . $app->id,
                        'title' => "APP-{$app->id} • {$candName}",
                        'subtitle' => "{$cnic}Job: " . ($app->campaign?->title ?? 'Recruitment Position'),
                        'url' => "$baseRoute/applications?search=" . urlencode((string)$app->id),
                        'category' => 'Applications',
                        'category_key' => 'applications',
                        'badge' => $app->status_label ?? $app->status,
                        'badge_style' => $this->getStatusBadgeClass($app->status),
                        'icon' => 'file-text',
                        'meta' => $app->created_at ? $app->created_at->format('d M Y') : null,
                    ];
                }
            } elseif ($isApplicant) {
                // Candidate searching their own applications only
                $appQuery = Application::where('user_id', $user->id)
                    ->with(['campaign.advertisement', 'billingLedger'])
                    ->orderBy('created_at', 'desc');

                if ($hasSearch) {
                    $rawId = preg_replace('/[^0-9]/', '', $searchTerm);
                    $isAppGeneric = str_contains(strtolower($searchTerm), 'app');
                    $appQuery->where(function ($q) use ($likeTerm, $rawId, $isAppGeneric) {
                        $q->where('id', 'like', $likeTerm)
                          ->orWhere('status', 'like', $likeTerm)
                          ->orWhereHas('campaign', function ($cq) use ($likeTerm) {
                              $cq->where('title', 'like', $likeTerm)
                                 ->orWhere('job_code', 'like', $likeTerm);
                          });

                        if (!empty($rawId)) {
                            $q->orWhere('id', $rawId);
                        }
                        if ($isAppGeneric) {
                            $q->orWhereRaw('1 = 1');
                        }
                    });
                }

                $applications = $appQuery->limit(5)->get();

                foreach ($applications as $app) {
                    $results[] = [
                        'id' => 'app_' . $app->id,
                        'title' => "My Application #{$app->id} — " . ($app->campaign?->title ?? 'Post'),
                        'subtitle' => "Status: " . ($app->status_label ?? $app->status) . ($app->billingLedger ? " • Fee: " . $app->billingLedger->status : ''),
                        'url' => '/applicant/dashboard',
                        'category' => 'Applications',
                        'category_key' => 'applications',
                        'badge' => $app->status_label ?? $app->status,
                        'badge_style' => $this->getStatusBadgeClass($app->status),
                        'icon' => 'file-text',
                        'meta' => $app->created_at ? $app->created_at->format('d M Y') : null,
                    ];
                }
            } elseif ($isScrutiny) {
                // Scrutiny committee searching applications in scrutiny queue
                $appQuery = Application::whereIn('status', ['SUBMITTED', 'UNDER_REVIEW', 'ELIGIBLE', 'INELIGIBLE'])
                    ->with(['campaign', 'user.basicInfo'])
                    ->orderBy('created_at', 'desc');

                if ($hasSearch) {
                    $appQuery->where(function ($q) use ($likeTerm) {
                        $q->where('id', 'like', $likeTerm)
                          ->orWhereHas('user.basicInfo', function ($bq) use ($likeTerm) {
                              $bq->where('first_name', 'like', $likeTerm)
                                 ->orWhere('last_name', 'like', $likeTerm)
                                 ->orWhere('cnic_number', 'like', $likeTerm);
                          })
                          ->orWhereHas('campaign', fn($cq) => $cq->where('title', 'like', $likeTerm));
                    });
                }

                $applications = $appQuery->limit(5)->get();

                foreach ($applications as $app) {
                    $candName = $app->user?->basicInfo ? trim(($app->user->basicInfo->first_name ?? '') . ' ' . ($app->user->basicInfo->last_name ?? '')) : 'Candidate';
                    $results[] = [
                        'id' => 'scrutiny_app_' . $app->id,
                        'title' => "Dossier #{$app->id} — {$candName}",
                        'subtitle' => "Position: " . ($app->campaign?->title ?? 'N/A') . " • Scrutiny Queue",
                        'url' => "/scrutiny/verification/" . $app->id,
                        'category' => 'Applications',
                        'category_key' => 'applications',
                        'badge' => 'SCRUTINIZE',
                        'badge_style' => 'badge-purple',
                        'icon' => 'file-text',
                        'meta' => $app->status_label ?? $app->status,
                    ];
                }
            }
        }

        // 5. Candidate Profiles
        if ($this->activeCategory === 'all' || $this->activeCategory === 'candidates') {
            if ($isSuperAdmin || $isRegistrar || $user->can('view-basic-info')) {
                $cQuery = BasicInfo::with('user')->orderBy('created_at', 'desc');
                if ($hasSearch) {
                    $cQuery->where(function ($q) use ($likeTerm) {
                        $q->where('first_name', 'like', $likeTerm)
                          ->orWhere('last_name', 'like', $likeTerm)
                          ->orWhere('cnic_number', 'like', $likeTerm)
                          ->orWhere('phone_number', 'like', $likeTerm)
                          ->orWhere('secondary_email', 'like', $likeTerm)
                          ->orWhereHas('user', fn($uq) => $uq->where('email', 'like', $likeTerm));
                    });
                }
                $candidates = $cQuery->limit(5)->get();

                foreach ($candidates as $cand) {
                    $fullName = trim(($cand->first_name ?? '') . ' ' . ($cand->last_name ?? ''));
                    $results[] = [
                        'id' => 'cand_' . $cand->id,
                        'title' => $fullName ?: 'Candidate Profile #' . $cand->id,
                        'subtitle' => "CNIC: {$cand->cnic_number} • Phone: {$cand->phone_number} • {$cand->user?->email}",
                        'url' => "$baseRoute/candidates?search=" . urlencode($cand->cnic_number ?: $fullName),
                        'category' => 'Candidate Profiles',
                        'category_key' => 'candidates',
                        'badge' => $cand->is_locked ? 'LOCKED' : 'EDITABLE',
                        'badge_style' => $cand->is_locked ? 'badge-amber' : 'badge-emerald',
                        'icon' => 'users',
                        'meta' => $cand->track_type ?: 'General',
                    ];
                }
            }
        }

        // 6. Reviewers & Experts
        if ($this->activeCategory === 'all' || $this->activeCategory === 'reviewers') {
            if ($isSuperAdmin || $isRegistrar || $user->can('manage-reviewers')) {
                $rQuery = ReviewerProfile::orderBy('created_at', 'desc');
                if ($hasSearch) {
                    $rQuery->where(function ($q) use ($likeTerm) {
                        $q->where('name', 'like', $likeTerm)
                          ->orWhere('subject', 'like', $likeTerm)
                          ->orWhere('university', 'like', $likeTerm)
                          ->orWhere('country', 'like', $likeTerm)
                          ->orWhere('academic_rank', 'like', $likeTerm)
                          ->orWhere('specialization', 'like', $likeTerm);
                    });
                }
                $reviewers = $rQuery->limit(5)->get();

                foreach ($reviewers as $rev) {
                    $results[] = [
                        'id' => 'rev_' . $rev->id,
                        'title' => $rev->name,
                        'subtitle' => "{$rev->academic_rank} • {$rev->university} • Field: {$rev->subject}",
                        'url' => "$baseRoute/reviewers?search=" . urlencode($rev->name),
                        'category' => 'Reviewers & Experts',
                        'category_key' => 'reviewers',
                        'badge' => $rev->is_active ? 'ACTIVE' : 'INACTIVE',
                        'badge_style' => $rev->is_active ? 'badge-purple' : 'badge-slate',
                        'icon' => 'award',
                        'meta' => $rev->country ?: 'International',
                    ];
                }
            }
        }

        // 7. Payments & Vouchers
        if ($this->activeCategory === 'all' || $this->activeCategory === 'payments') {
            if ($isSuperAdmin || $isRegistrar || $user->can('view-payment')) {
                $pQuery = BillingLedger::with(['application.user.basicInfo'])->orderBy('created_at', 'desc');
                if ($hasSearch) {
                    $pQuery->where(function ($q) use ($likeTerm) {
                        $q->where('consumer_id', 'like', $likeTerm)
                          ->orWhere('transaction_id', 'like', $likeTerm)
                          ->orWhere('status', 'like', $likeTerm)
                          ->orWhereHas('application.user.basicInfo', function ($bq) use ($likeTerm) {
                              $bq->where('first_name', 'like', $likeTerm)
                                 ->orWhere('last_name', 'like', $likeTerm)
                                 ->orWhere('cnic_number', 'like', $likeTerm);
                          });
                    });
                }
                $ledgers = $pQuery->limit(5)->get();

                foreach ($ledgers as $voucher) {
                    $applicantName = $voucher->application?->user?->basicInfo ? trim(($voucher->application->user->basicInfo->first_name ?? '') . ' ' . ($voucher->application->user->basicInfo->last_name ?? '')) : 'Applicant';
                    $results[] = [
                        'id' => 'pay_' . $voucher->id,
                        'title' => "Voucher #{$voucher->consumer_id} — PKR " . number_format($voucher->amount, 2),
                        'subtitle' => "Candidate: {$applicantName} • Trans ID: " . ($voucher->transaction_id ?: 'Pending Settlement'),
                        'url' => "$baseRoute/payments?search=" . urlencode((string)$voucher->consumer_id),
                        'category' => 'Payments & Vouchers',
                        'category_key' => 'payments',
                        'badge' => $voucher->status ?: 'UNPAID',
                        'badge_style' => $voucher->status === 'SETTLED' || $voucher->status === 'PAID' ? 'badge-emerald' : 'badge-amber',
                        'icon' => 'credit-card',
                        'meta' => $voucher->created_at ? $voucher->created_at->format('d M Y') : null,
                    ];
                }
            }
        }

        // 8. Constitutions & Pipelines
        if ($this->activeCategory === 'all' || $this->activeCategory === 'constitutions') {
            if ($isSuperAdmin || $isRegistrar || $user->can('tune-constitution')) {
                $constQuery = CommitteeConstitution::orderBy('created_at', 'desc');
                if ($hasSearch) {
                    $constQuery->where(function ($q) use ($likeTerm) {
                        $q->where('name', 'like', $likeTerm)
                          ->orWhere('committee_type', 'like', $likeTerm)
                          ->orWhere('responsible_stage', 'like', $likeTerm);
                    });
                }
                $constitutions = $constQuery->limit(3)->get();

                foreach ($constitutions as $const) {
                    $results[] = [
                        'id' => 'const_' . $const->id,
                        'title' => $const->name,
                        'subtitle' => "Type: {$const->committee_type} • Responsible: {$const->responsible_stage} • Members: {$const->required_total}",
                        'url' => "$baseRoute/criteria?search=" . urlencode($const->name),
                        'category' => 'Boards & Pipelines',
                        'category_key' => 'constitutions',
                        'badge' => 'BOARD',
                        'badge_style' => 'badge-cyan',
                        'icon' => 'sliders',
                        'meta' => $const->is_active ? 'Active' : 'Draft',
                    ];
                }

                $flowQuery = RecruitmentProcessFlow::orderBy('created_at', 'desc');
                if ($hasSearch) {
                    $flowQuery->where(function ($q) use ($likeTerm) {
                        $q->where('name', 'like', $likeTerm)
                          ->orWhere('sector', 'like', $likeTerm);
                    });
                }
                $flows = $flowQuery->limit(3)->get();

                foreach ($flows as $flow) {
                    $results[] = [
                        'id' => 'flow_' . $flow->id,
                        'title' => "Pipeline: {$flow->name}",
                        'subtitle' => "Sector: {$flow->sector} • Multi-stage recruitment flow lifecycle",
                        'url' => "$baseRoute/pipelines?search=" . urlencode($flow->name),
                        'category' => 'Boards & Pipelines',
                        'category_key' => 'constitutions',
                        'badge' => 'PIPELINE',
                        'badge_style' => 'badge-indigo',
                        'icon' => 'sliders',
                        'meta' => $flow->is_active ? 'Active Flow' : 'Draft',
                    ];
                }
            }
        }

        // 9. User Accounts
        if ($this->activeCategory === 'all' || $this->activeCategory === 'users') {
            if ($isSuperAdmin || $isRegistrar || $user->can('view-user')) {
                $uQuery = User::with(['basicInfo', 'roles'])->orderBy('created_at', 'desc');
                if ($hasSearch) {
                    $uQuery->where(function ($q) use ($likeTerm) {
                        $q->where('email', 'like', $likeTerm)
                          ->orWhereHas('basicInfo', function ($bq) use ($likeTerm) {
                              $bq->where('first_name', 'like', $likeTerm)
                                 ->orWhere('last_name', 'like', $likeTerm)
                                 ->orWhere('cnic_number', 'like', $likeTerm);
                          });
                    });
                }
                $users = $uQuery->limit(5)->get();

                foreach ($users as $u) {
                    $rolesString = $u->roles->pluck('name')->join(', ') ?: 'No Role';
                    $personName = $u->basicInfo ? trim(($u->basicInfo->first_name ?? '') . ' ' . ($u->basicInfo->last_name ?? '')) : null;
                    $results[] = [
                        'id' => 'user_' . $u->id,
                        'title' => $personName ? "{$personName} ({$u->email})" : $u->email,
                        'subtitle' => "Assigned Role(s): {$rolesString} • Status: " . ($u->is_active ? 'Active' : 'Suspended'),
                        'url' => "$baseRoute/users?search=" . urlencode($u->email),
                        'category' => 'User Accounts',
                        'category_key' => 'users',
                        'badge' => $u->is_active ? 'ACTIVE' : 'SUSPENDED',
                        'badge_style' => $u->is_active ? 'badge-blue' : 'badge-rose',
                        'icon' => 'shield',
                        'meta' => 'ID #' . $u->id,
                    ];
                }
            }
        }

        return $results;
    }

    private function getStatusBadgeClass(?string $status): string
    {
        return match ($status) {
            'APPROVED', 'ELIGIBLE', 'PAID', 'SETTLED' => 'badge-emerald',
            'UNDER_REVIEW' => 'badge-purple',
            'INCOMPLETE_DOSSIER', 'APPEALED', 'PENDING' => 'badge-amber',
            'REJECTED', 'INELIGIBLE', 'DISQUALIFIED' => 'badge-rose',
            'SUBMITTED' => 'badge-indigo',
            default => 'badge-slate',
        };
    }

    public function render()
    {
        $categories = $this->getAvailableCategories();
        $results = $this->isOpen ? $this->getSearchResults() : [];

        return view('livewire.universal-search-palette', [
            'categories' => $categories,
            'results' => $results,
        ]);
    }
}
