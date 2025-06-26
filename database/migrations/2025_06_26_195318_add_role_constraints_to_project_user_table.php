<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add a check constraint to ensure only creators and assignees can be project members
        // Note: This uses raw SQL as Laravel doesn't have native support for check constraints

        if (DB::getDriverName() === 'mysql') {
            // For MySQL, we'll create a trigger instead of a check constraint
            DB::unprepared('
                CREATE TRIGGER check_project_user_role_insert
                BEFORE INSERT ON project_user
                FOR EACH ROW
                BEGIN
                    DECLARE user_role VARCHAR(255);
                    SELECT role INTO user_role FROM users WHERE id = NEW.user_id;

                    IF user_role NOT IN ("creator", "assignee") THEN
                        SIGNAL SQLSTATE "45000"
                        SET MESSAGE_TEXT = "Only users with creator or assignee roles can be assigned to projects";
                    END IF;
                END
            ');

            DB::unprepared('
                CREATE TRIGGER check_project_user_role_update
                BEFORE UPDATE ON project_user
                FOR EACH ROW
                BEGIN
                    DECLARE user_role VARCHAR(255);
                    SELECT role INTO user_role FROM users WHERE id = NEW.user_id;

                    IF user_role NOT IN ("creator", "assignee") THEN
                        SIGNAL SQLSTATE "45000"
                        SET MESSAGE_TEXT = "Only users with creator or assignee roles can be assigned to projects";
                    END IF;
                END
            ');
        } elseif (DB::getDriverName() === 'pgsql') {
            // For PostgreSQL
            DB::unprepared('
                ALTER TABLE project_user
                ADD CONSTRAINT check_project_user_role
                CHECK (
                    (SELECT role FROM users WHERE id = user_id) IN (\'creator\', \'assignee\')
                )
            ');
        } elseif (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't support adding constraints to existing tables
            // Role constraints will be enforced at application level for SQLite
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared('DROP TRIGGER IF EXISTS check_project_user_role_insert');
            DB::unprepared('DROP TRIGGER IF EXISTS check_project_user_role_update');
        } elseif (DB::getDriverName() === 'pgsql') {
            DB::unprepared('ALTER TABLE project_user DROP CONSTRAINT IF EXISTS check_project_user_role');
        }
    }
};
