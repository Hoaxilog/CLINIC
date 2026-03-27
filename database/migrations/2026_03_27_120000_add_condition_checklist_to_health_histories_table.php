<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('health_histories')) {
            return;
        }

        Schema::table('health_histories', function (Blueprint $table) {
            $table->boolean('is_chest_pain_angina')->default(false);
            $table->boolean('is_shortness_of_breath')->default(false);
            $table->boolean('is_heart_disease_heart_attack')->default(false);
            $table->boolean('is_heart_surgery')->default(false);
            $table->boolean('is_artificial_heart_valve_pacemaker')->default(false);
            $table->boolean('is_rheumatic_fever_heart_disease')->default(false);
            $table->boolean('is_heart_murmur')->default(false);
            $table->boolean('is_mitral_valve_prolapse')->default(false);
            $table->boolean('is_high_low_blood_pressure')->default(false);
            $table->boolean('is_stroke')->default(false);
            $table->boolean('is_respiratory_lung_problem')->default(false);
            $table->boolean('is_emphysema')->default(false);
            $table->boolean('is_asthma')->default(false);
            $table->boolean('is_tuberculosis')->default(false);
            $table->boolean('is_blood_disease')->default(false);
            $table->boolean('is_bleeding_problems_disorders')->default(false);
            $table->boolean('is_diabetes')->default(false);
            $table->boolean('is_liver_problem_jaundice_hepatitis')->default(false);
            $table->boolean('is_kidney_bladder_problem')->default(false);
            $table->boolean('is_ulcers_hyperacidity')->default(false);
            $table->boolean('is_tumors_cancer_malignancies')->default(false);
            $table->boolean('is_aids_hiv_positive')->default(false);
            $table->boolean('is_fainting_epilepsy_seizures')->default(false);
            $table->boolean('is_mental_health_disorder')->default(false);
            $table->boolean('is_other_disease_condition_problem')->default(false);
            $table->text('what_other_disease_condition_problem')->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('health_histories')) {
            return;
        }

        Schema::table('health_histories', function (Blueprint $table) {
            $table->dropColumn([
                'is_chest_pain_angina',
                'is_shortness_of_breath',
                'is_heart_disease_heart_attack',
                'is_heart_surgery',
                'is_artificial_heart_valve_pacemaker',
                'is_rheumatic_fever_heart_disease',
                'is_heart_murmur',
                'is_mitral_valve_prolapse',
                'is_high_low_blood_pressure',
                'is_stroke',
                'is_respiratory_lung_problem',
                'is_emphysema',
                'is_asthma',
                'is_tuberculosis',
                'is_blood_disease',
                'is_bleeding_problems_disorders',
                'is_diabetes',
                'is_liver_problem_jaundice_hepatitis',
                'is_kidney_bladder_problem',
                'is_ulcers_hyperacidity',
                'is_tumors_cancer_malignancies',
                'is_aids_hiv_positive',
                'is_fainting_epilepsy_seizures',
                'is_mental_health_disorder',
                'is_other_disease_condition_problem',
                'what_other_disease_condition_problem',
            ]);
        });
    }
};
