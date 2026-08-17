<?php

declare(strict_types=1);

namespace OliverKroener\OkPriveConsent\Upgrades;

use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Upgrades\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Core\Upgrades\UpgradeWizardInterface;

/**
 * Moves the consent banner settings from sys_template to the site root page.
 *
 * Up to v5 the banner script and the enable flag were stored on the sys_template
 * record of the site root page. Sites driven by site sets have no sys_template
 * record at all, so the settings now live on the pages record of the site root.
 */
#[UpgradeWizard('okPriveConsentMigrateStorageToPages')]
final class MigrateConsentStorageToPagesUpgradeWizard implements UpgradeWizardInterface
{
    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {}

    public function getTitle(): string
    {
        return 'EXT:ok_prive_consent: Move consent banner settings from sys_template to pages';
    }

    public function getDescription(): string
    {
        return 'Copies tx_ok_prive_cookie_consent_banner_script and '
            . 'tx_ok_prive_cookie_consent_banner_enabled from existing sys_template records '
            . 'to the corresponding site root page, so the backend module keeps working on '
            . 'sites that use site sets instead of TypoScript records.';
    }

    public function updateNecessary(): bool
    {
        return $this->getRowsToMigrate() !== [];
    }

    public function executeUpdate(): bool
    {
        $connection = $this->connectionPool->getConnectionForTable('pages');

        foreach ($this->getRowsToMigrate() as $row) {
            $connection->update(
                'pages',
                [
                    'tx_ok_prive_cookie_consent_banner_script' => (string)$row['tx_ok_prive_cookie_consent_banner_script'],
                    'tx_ok_prive_cookie_consent_banner_enabled' => (int)$row['tx_ok_prive_cookie_consent_banner_enabled'],
                ],
                ['uid' => (int)$row['pid']]
            );
        }

        return true;
    }

    /**
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [DatabaseUpdatedPrerequisite::class];
    }

    /**
     * sys_template rows that still carry settings which have not been copied to their page yet.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getRowsToMigrate(): array
    {
        $schemaManager = $this->connectionPool->getConnectionForTable('sys_template')->createSchemaManager();
        if (!$schemaManager->tablesExist(['sys_template'])) {
            return [];
        }

        $columns = array_map(
            static fn($column): string => $column->getName(),
            $schemaManager->listTableColumns('sys_template')
        );
        if (!in_array('tx_ok_prive_cookie_consent_banner_script', $columns, true)) {
            return [];
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_template');
        $queryBuilder->getRestrictions()->removeAll()->add(new DeletedRestriction());

        $rows = $queryBuilder
            ->select(
                'sys_template.pid',
                'sys_template.tx_ok_prive_cookie_consent_banner_script',
                'sys_template.tx_ok_prive_cookie_consent_banner_enabled'
            )
            ->from('sys_template')
            ->innerJoin(
                'sys_template',
                'pages',
                'pages',
                $queryBuilder->expr()->eq('pages.uid', $queryBuilder->quoteIdentifier('sys_template.pid'))
            )
            ->where(
                $queryBuilder->expr()->neq(
                    'sys_template.tx_ok_prive_cookie_consent_banner_script',
                    $queryBuilder->createNamedParameter('')
                ),
                $queryBuilder->expr()->eq(
                    'pages.tx_ok_prive_cookie_consent_banner_script',
                    $queryBuilder->createNamedParameter('')
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();

        return $rows;
    }
}
