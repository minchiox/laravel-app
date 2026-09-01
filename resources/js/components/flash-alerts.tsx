import { Alert, AlertDescription } from '@/components/ui/alert';
import type { SharedPageProps } from '@/types';

export default function FlashAlerts({ flash }: { flash: SharedPageProps['flash'] }) {
    if (!flash.success && !flash.error) {
        return null;
    }

    return (
        <div className="mb-6 space-y-3">
            {flash.success && (
                <Alert variant="success">
                    <AlertDescription>{flash.success}</AlertDescription>
                </Alert>
            )}
            {flash.error && (
                <Alert variant="destructive">
                    <AlertDescription>{flash.error}</AlertDescription>
                </Alert>
            )}
        </div>
    );
}
