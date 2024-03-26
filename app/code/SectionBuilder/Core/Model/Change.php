<?php
namespace SectionBuilder\Core\Model;

class Change
{
    public function replaceData($resourceModel, $data, $conditionKey, $conditionVal, $targetKey)
    {
        $tableName = $resourceModel->getMainTable();
        $select = $resourceModel->getConnection()->select()
            ->from($tableName)
            ->where("$conditionKey = ?", $conditionVal);
        $oldData = $resourceModel->getConnection()->fetchAll($select);
        $oldData = array_column($oldData, $targetKey);
        $dataToRemove = array_diff($oldData, $data);
        $dataToAdd = array_diff($data, $oldData);

        if ($dataToRemove) {
            $resourceModel->getConnection()->delete(
                $tableName,
                [
                    "$conditionKey = ?" => $conditionVal,
                    "$targetKey IN (?)" => array_unique($dataToRemove)
                ]
            );
        }

        foreach ($dataToAdd as $id) {
            if ($id) {
                $dataToInsert[] = [
                    "$conditionKey" => $conditionVal,
                    "$targetKey" => $id
                ];
            }
        }

        if (!empty($dataToInsert)) {
            $resourceModel->getConnection()->insertMultiple(
                $tableName,
                $dataToInsert
            );
        }
    }
}
