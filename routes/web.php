<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\FileAccessController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\InvoicePdfController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\Site\ConsultationController;
use App\Http\Controllers\Site\InquiryController;
use App\Http\Controllers\Site\WebsiteController;
use App\Http\Controllers\Webhook\PaymentWebhookController;
use App\Http\Controllers\Webhook\WhatsAppWebhookController;
use App\Livewire\Client\Dashboard as ClientDashboard;
use App\Livewire\Client\Gallery as ClientGallery;
use App\Livewire\Client\Payments as ClientPayments;
use App\Livewire\Client\ProjectShow as ClientProject;
use App\Livewire\Client\Timeline as ClientTimeline;
use App\Livewire\Platform\WebsiteEditor;
use App\Livewire\Profile\Edit as ProfileEdit;
use App\Livewire\Studio\Calendar\Index as CalendarIndex;
use App\Livewire\Studio\Clients\Create as ClientsCreate;
use App\Livewire\Studio\Clients\Edit as ClientsEdit;
use App\Livewire\Studio\Clients\Index as ClientsIndex;
use App\Livewire\Studio\Consultations\Create as ConsultationsCreate;
use App\Livewire\Studio\Consultations\Edit as ConsultationsEdit;
use App\Livewire\Studio\Consultations\Index as ConsultationsIndex;
use App\Livewire\Studio\Dashboard as StudioDashboard;
use App\Livewire\Studio\Files\Index as FilesIndex;
use App\Livewire\Studio\Invoices\Create as InvoicesCreate;
use App\Livewire\Studio\Invoices\Edit as InvoicesEdit;
use App\Livewire\Studio\Invoices\Index as InvoicesIndex;
use App\Livewire\Studio\Leads\Form as LeadForm;
use App\Livewire\Studio\Leads\Index as LeadsIndex;
use App\Livewire\Studio\Leads\Pipeline as LeadsPipeline;
use App\Livewire\Studio\Leads\Show as LeadShow;
use App\Livewire\Studio\Packages\Create as PackagesCreate;
use App\Livewire\Studio\Packages\Edit as PackagesEdit;
use App\Livewire\Studio\Packages\Index as PackagesIndex;
use App\Livewire\Studio\Packages\Show as PackageShow;
use App\Livewire\Studio\Production\Create as ProductionCreate;
use App\Livewire\Studio\Production\Edit as ProductionEdit;
use App\Livewire\Studio\Production\Index as ProductionIndex;
use App\Livewire\Studio\Payments\Create as PaymentsCreate;
use App\Livewire\Studio\Payments\Index as PaymentsIndex;
use App\Livewire\Studio\Projects\Create as ProjectsCreate;
use App\Livewire\Studio\Projects\Edit as ProjectsEdit;
use App\Livewire\Studio\Projects\Index as ProjectsIndex;
use App\Livewire\Studio\Projects\Show as ProjectShow;
use App\Livewire\Studio\Reports\Index as ReportsIndex;
use App\Livewire\Studio\Settings\EmailSmtp;
use App\Livewire\Studio\Settings\GoogleDrive as GoogleDriveSettings;
use App\Livewire\Studio\Settings\Index as SettingsIndex;
use App\Livewire\Studio\Settings\PaymentGateway as PaymentGatewaySettings;
use App\Livewire\Studio\Tasks\Create as TasksCreate;
use App\Livewire\Studio\Tasks\Edit as TasksEdit;
use App\Livewire\Studio\Tasks\Index as TasksIndex;
use App\Livewire\Studio\Team\Create as TeamCreate;
use App\Livewire\Studio\Team\RoleCreate as TeamRoleCreate;
use App\Livewire\Studio\Team\RoleEdit as TeamRoleEdit;
use App\Livewire\Studio\Team\Roles as TeamRoles;
use App\Livewire\Studio\Team\Edit as TeamEdit;
use App\Livewire\Studio\Team\Index as TeamIndex;
use App\Livewire\Studio\Team\Permissions as TeamPermissions;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class)->name('health');
Route::get('/robots.txt', [SeoController::class, 'robots']);
Route::get('/sitemap.xml', [SeoController::class, 'sitemap']);

Route::get('/', [WebsiteController::class, 'home'])->name('home');
Route::get('/production/{slug}', [WebsiteController::class, 'production'])->name('public.production.show');
Route::get('/services/{slug}', [WebsiteController::class, 'service'])->name('public.services.show');
Route::get('/packages/{slug}', [WebsiteController::class, 'service'])->name('public.packages.show');
Route::get('/projects/{slug}', [WebsiteController::class, 'project'])->name('public.projects.show');
Route::get('/portfolio/{slug}', [WebsiteController::class, 'project'])->name('public.portfolio.show');
Route::get('/{page}', [WebsiteController::class, 'page'])
    ->whereIn('page', ['about', 'services', 'production', 'packages', 'portfolio', 'gallery', 'testimonials', 'faq', 'contact', 'book-consultation', 'terms-and-conditions', 'privacy-policy', 'cookie-policy']);

Route::post('/inquiry', [InquiryController::class, 'store'])
    ->middleware('throttle:8,1')
    ->name('inquiry.store');
Route::post('/consultations', [ConsultationController::class, 'store'])
    ->middleware('throttle:8,1')
    ->name('consultation.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::get('/studio/login', [AuthenticatedSessionController::class, 'create'])->name('studio.login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:8,1');
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::post('/webhooks/payments/{provider}', PaymentWebhookController::class)->name('webhooks.payments');
Route::get('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'verify']);
Route::post('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'store']);

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/profile', ProfileEdit::class)->name('profile.edit');

    Route::get('/files/signed', [FileAccessController::class, 'signed'])->name('files.signed');
    Route::get('/files/{file}', [FileAccessController::class, 'show'])->name('files.show');
    Route::get('/invoices/{invoice}/pdf', InvoicePdfController::class)->name('invoices.pdf');

    Route::prefix('app')->name('app.')->middleware('studio.staff')->group(function () {
        Route::get('/', StudioDashboard::class)->name('dashboard');
        Route::get('/leads', LeadsIndex::class)->name('leads.index');
        Route::get('/leads/pipeline', LeadsPipeline::class)->name('leads.pipeline');
        Route::get('/leads/create', LeadForm::class)->name('leads.create');
        Route::get('/leads/{lead}', LeadShow::class)->name('leads.show');
        Route::get('/clients', ClientsIndex::class)->name('clients.index');
        Route::get('/clients/create', ClientsCreate::class)->name('clients.create');
        Route::get('/clients/{client}/edit', ClientsEdit::class)->name('clients.edit');
        Route::get('/consultations', ConsultationsIndex::class)->name('consultations.index');
        Route::get('/consultations/create', ConsultationsCreate::class)->name('consultations.create');
        Route::get('/consultations/{slot}/edit', ConsultationsEdit::class)->name('consultations.edit');
        Route::get('/packages', PackagesIndex::class)->name('packages.index');
        Route::get('/packages/create', PackagesCreate::class)->name('packages.create');
        Route::get('/packages/{package}/edit', PackagesEdit::class)->name('packages.edit');
        Route::get('/packages/{package}', PackageShow::class)->name('packages.show');
        Route::get('/production', ProductionIndex::class)->name('production.index');
        Route::get('/production/create', ProductionCreate::class)->name('production.create');
        Route::get('/production/{project}/edit', ProductionEdit::class)->name('production.edit');
        Route::get('/projects', ProjectsIndex::class)->name('projects.index');
        Route::get('/projects/create', ProjectsCreate::class)->name('projects.create');
        Route::get('/projects/{project}/edit', ProjectsEdit::class)->name('projects.edit');
        Route::get('/projects/{project}', ProjectShow::class)->name('projects.show');
        Route::get('/calendar', CalendarIndex::class)->name('calendar');
        Route::get('/team', TeamIndex::class)->name('team.index');
        Route::get('/team/roles', TeamRoles::class)->name('team.roles');
        Route::get('/team/roles/create', TeamRoleCreate::class)->name('team.roles.create');
        Route::get('/team/roles/{role}/edit', TeamRoleEdit::class)->name('team.roles.edit');
        Route::get('/team/create', TeamCreate::class)->name('team.create');
        Route::get('/team/permissions', TeamPermissions::class)->name('team.permissions');
        Route::get('/team/{member}/edit', TeamEdit::class)->name('team.edit');
        Route::get('/tasks', TasksIndex::class)->name('tasks.index');
        Route::get('/tasks/create', TasksCreate::class)->name('tasks.create');
        Route::get('/tasks/{task}/edit', TasksEdit::class)->name('tasks.edit');
        Route::get('/payments', PaymentsIndex::class)->name('payments.index');
        Route::get('/payments/create', PaymentsCreate::class)->name('payments.create');
        Route::get('/invoices', InvoicesIndex::class)->name('invoices.index');
        Route::get('/invoices/create', InvoicesCreate::class)->name('invoices.create');
        Route::get('/invoices/{invoice}/edit', InvoicesEdit::class)->name('invoices.edit');
        Route::redirect('/galleries', '/app/projects');
        Route::get('/files', FilesIndex::class)->name('files.index');
        Route::get('/reports', ReportsIndex::class)->name('reports.index');
        Route::get('/settings', SettingsIndex::class)->name('settings.index');
        Route::get('/settings/payments', PaymentGatewaySettings::class)->name('settings.payments');
        Route::get('/settings/email', EmailSmtp::class)->name('settings.email');
        Route::get('/settings/google-drive', GoogleDriveSettings::class)->name('settings.google-drive');
        Route::get('/website', WebsiteEditor::class)->name('website');
    });

    Route::prefix('client')->name('client.')->middleware('role:client')->group(function () {
        Route::get('/', ClientDashboard::class)->name('dashboard');
        Route::get('/project/{project}', ClientProject::class)->name('project');
        Route::get('/payments', ClientPayments::class)->name('payments');
        Route::get('/gallery', ClientGallery::class)->name('gallery');
        Route::get('/timeline', ClientTimeline::class)->name('timeline');
    });

    Route::redirect('/platform', '/app');
    Route::redirect('/platform/{any}', '/app')->where('any', '.*');
});
